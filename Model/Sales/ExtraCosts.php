<?php

/*
 * Copyright (C) Licentia, Unipessoal LDA
 *
 * NOTICE OF LICENSE
 *
 *  This source file is subject to the EULA
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  https://www.greenflyingpanda.com/panda-license.txt
 *
 *  @title      Licentia Panda - Magento® Sales Automation Extension
 *  @package    Licentia
 *  @author     Bento Vilas Boas <bento@licentia.pt>
 *  @copyright  Copyright (c) Licentia - https://licentia.pt
 *  @license    https://www.greenflyingpanda.com/panda-license.txt
 *
 */

namespace Licentia\Equity\Model\Sales;

/**
 * Class ExtraCosts
 *
 * @package Licentia\Panda\Model\Sales
 */
class ExtraCosts extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_sales_extra_costs';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'panda_sales_extra_costs';

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected $pandaHelper;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(\Licentia\Equity\Model\ResourceModel\Sales\ExtraCosts::class);
    }

    /**
     * ExtraCosts constructor.
     *
     * @param \Licentia\Equity\Helper\Data                                 $pandaHelper
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Licentia\Equity\Helper\Data $pandaHelper,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->pandaHelper = $pandaHelper;
    }

    /**
     * @param \Magento\Sales\Model\Order|null $order
     *
     * @return $this
     */
    public function updateOrdersOtherCosts(\Magento\Sales\Model\Order $order = null)
    {

        $resource = $this->getResource();
        $connection = $resource->getConnection();

        if (!$order) {
            $connection->update(
                $resource->getTable('sales_order'),
                [
                    'panda_extra_costs' => null,
                ]
            );
        }

        $investments = $connection->select()
                                  ->from(
                                      $resource->getMainTable(),
                                      [
                                          '*',
                                          'total' => new \Zend_Db_Expr(
                                              'IF(
                                                    datediff(to_date , NOW()) > 0 ,
                                                   ABS(SUM(investment) / DATEDIFF(to_date , from_date) * ' .
                                              'datediff(from_date , NOW())) ,
                                                    SUM(investment)
                                                )'
                                          ),
                                      ]
                                  )
                                  ->where("(type = 'marketing' AND investment > 0) OR (type!='marketing')")
                                  ->group('cost_id');
        if ($order) {
            $investments->where('to_date>=?', substr($order->getCreatedAt(), 0, 10));
            $investments->where('from_date<=?', substr($order->getCreatedAt(), 0, 10));
        }

        $result = $connection->fetchAll($investments);

        foreach ($result as $extraCost) {
            $orders = $connection->select()
                                 ->from(
                                     $resource->getTable('sales_order'),
                                     ['entity_id', 'shipping_method', 'base_grand_total']
                                 )
                                 ->where('created_at>=?', $extraCost['from_date'])
                                 ->where('created_at<=?', $extraCost['to_date'])
                                 ->where('state IN (?)', ['closed', 'complete']);

            if ($extraCost['type'] == 'marketing') {
                if ($extraCost['campaign'] == '*') {
                    $orders->where('panda_acquisition_campaign IS NOT NULL');
                } elseif (strlen($extraCost['campaign']) > 0) {
                    $campaigns = explode(',', $extraCost['campaign']);
                    $campaigns = array_map('trim', $campaigns);

                    if (stripos($extraCost['campaign'], '*') !== false) {

                        /** @var \Licentia\Panda\Model\Campaigns $campaign */
                        foreach ($campaigns as $campaign) {
                            $cond = str_replace('*', '%', $campaign);
                            $orders->where("panda_acquisition_campaign LIKE ? ", $cond);
                        }
                    } else {
                        $orders->where('panda_acquisition_campaign IN(?)', $campaigns);
                    }
                }
            }

            if ($extraCost['type'] == 'shipping') {
                $orders->where('shipping_method IN (?)', explode(',', $extraCost['shipping_methods']));
            }

            if ($extraCost['type'] == 'payment') {
                $orders->joinLeft(
                    ['p' => $resource->getTable('sales_order_payment')],
                    'p.parent_id=' . $resource->getTable('sales_order') . '.entity_id',
                    ['method']
                );
                $orders->where('method IN (?)', explode(',', $extraCost['payment_methods']));
            }

            $resultOrders = $connection->fetchAll($orders);
            $totalOrders = count($resultOrders);
            $ordersIds = [];
            $globalCost = 0;

            foreach ($resultOrders as $orderInfo) {
                $ordersIds[] = $orderInfo['entity_id'];

                $extraCostValue = 0;

                if ($totalOrders > 0) {
                    $extraCostValue = $extraCost['total'] / $totalOrders;
                }

                if ($extraCost['flat_fee'] > 0) {
                    $extraCostValue += $extraCost['flat_fee'];
                }

                if ($extraCost['variable_fee'] > 0) {
                    $extraCostValue += $extraCost['variable_fee'] * $orderInfo['base_grand_total'] / 100;
                }

                $connection->update(
                    $resource->getTable('sales_order'),
                    [
                        'panda_extra_costs' => new \Zend_Db_Expr('IFNULL(panda_extra_costs,0) + ' . $extraCostValue),
                    ],
                    [
                        'entity_id=?' => $orderInfo['entity_id'],
                    ]
                );

                $globalCost += $extraCostValue;
            }

            $updateData = [
                'number_orders' => $totalOrders,
                'cost'          => $totalOrders > 0 ? ($globalCost / $totalOrders) : 0,
                'global_cost'   => $globalCost,
                'updated_at'    => $this->pandaHelper->gmtDate(),
            ];

            if (!$order) {
                $updateData['affected_orders'] = implode(',', $ordersIds);
            }

            $connection->update(
                $resource->getTable('panda_sales_extra_costs'),
                $updateData,
                [
                    'cost_id=?' => $extraCost['cost_id'],
                ]
            );

        }

        return $this;
    }

    /**
     * @return array
     */
    public static function getCostTypesValues()
    {

        $return = [];

        $return[] = ['value' => 'marketing', 'label' => __('Marketing')];
        $return[] = ['value' => 'payments', 'label' => __('Payments')];
        $return[] = ['value' => 'shipping', 'label' => __('Shipping')];
        $return[] = ['value' => 'other', 'label' => __('Other')];

        return $return;
    }

    /**
     * @return array
     */
    public static function getCostTypesHash()
    {

        $options = [];
        foreach (self::getCostTypesValues() as $type) {
            $options[$type['value']] = $type['label'];
        }

        return $options;
    }

    /**
     * @param $costId
     *
     * @return $this
     */
    public function setCostId($costId)
    {

        return $this->setData('cost_id', $costId);
    }

    /**
     * @param $fromDate
     *
     * @return $this
     */
    public function setFromDate($fromDate)
    {

        return $this->setData('from_date', $fromDate);
    }

    /**
     * @param $toDate
     *
     * @return $this
     */
    public function setToDate($toDate)
    {

        return $this->setData('to_date', $toDate);
    }

    /**
     * @param $title
     *
     * @return $this
     */
    public function setTitle($title)
    {

        return $this->setData('title', $title);
    }

    /**
     * @param $campaign
     *
     * @return $this
     */
    public function setCampaign($campaign)
    {

        return $this->setData('campaign', $campaign);
    }

    /**
     * @param $note
     *
     * @return $this
     */
    public function setNote($note)
    {

        return $this->setData('note', $note);
    }

    /**
     * @param $investment
     *
     * @return $this
     */
    public function setInvestment($investment)
    {

        return $this->setData('investment', $investment);
    }

    /**
     * @param $target
     *
     * @return $this
     */
    public function setTarget($target)
    {

        return $this->setData('target', $target);
    }

    /**
     * @return mixed
     */
    public function getCostId()
    {

        return $this->getData('cost_id');
    }

    /**
     * @return mixed
     */
    public function getFromDate()
    {

        return $this->getData('from_date');
    }

    /**
     * @return mixed
     */
    public function getToDate()
    {

        return $this->getData('to_date');
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {

        return $this->getData('title');
    }

    /**
     * @return mixed
     */
    public function getCampaign()
    {

        return $this->getData('campaign');
    }

    /**
     * @return mixed
     */
    public function getNote()
    {

        return $this->getData('note');
    }

    /**
     * @return mixed
     */
    public function getInvestment()
    {

        return $this->getData('investment');
    }

    /**
     * @return mixed
     */
    public function getTarget()
    {

        return $this->getData('target');
    }

    /**
     * @param $cost
     *
     * @return $this
     */
    public function setCost($cost)
    {

        return $this->setData('cost', $cost);
    }

    /**
     * @param $numberOrders
     *
     * @return $this
     */
    public function setNumberOrders($numberOrders)
    {

        return $this->setData('number_orders', $numberOrders);
    }

    /**
     * @param $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {

        return $this->setData('updated_at', $updatedAt);
    }

    /**
     * @return mixed
     */
    public function getCost()
    {

        return $this->getData('cost');
    }

    /**
     * @return mixed
     */
    public function getNumberOrders()
    {

        return $this->getData('number_orders');
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {

        return $this->getData('updated_at');
    }

    /**
     * @param $type
     *
     * @return $this
     */
    public function setType($type)
    {

        return $this->setData('type', $type);
    }

    /**
     * @return mixed
     */
    public function getType()
    {

        return $this->getData('type');
    }

    /**
     * @param $paymentMethods
     *
     * @return $this
     */
    public function setPaymentMethods($paymentMethods)
    {

        return $this->setData('payment_methods', $paymentMethods);
    }

    /**
     * @param $shippingMethods
     *
     * @return $this
     */
    public function setShippingMethods($shippingMethods)
    {

        return $this->setData('shipping_methods', $shippingMethods);
    }

    /**
     * @return mixed
     */
    public function getPaymentMethods()
    {

        return $this->getData('payment_methods');
    }

    /**
     * @return mixed
     */
    public function getShippingMethods()
    {

        return $this->getData('shipping_methods');
    }

    /**
     * @param $variableFee
     *
     * @return $this
     */
    public function setVariableFee($variableFee)
    {

        return $this->setData('variable_fee', $variableFee);
    }

    /**
     * @param $fixedFee
     *
     * @return $this
     */
    public function setFixedFee($fixedFee)
    {

        return $this->setData('flat_fee', $fixedFee);
    }

    /**
     * @return mixed
     */
    public function getVariableFee()
    {

        return $this->getData('variable_fee');
    }

    /**
     * @return mixed
     */
    public function getFixedFee()
    {

        return $this->getData('flat_fee');
    }

    /**
     * @param $globalCost
     *
     * @return $this
     */
    public function setGlobalCost($globalCost)
    {

        return $this->setData('global_cost', $globalCost);
    }

    /**
     * @return mixed
     */
    public function getGlobalCost()
    {

        return $this->getData('global_cost');
    }

}
