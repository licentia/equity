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

namespace Licentia\Equity\Model\Segments\Condition;

use Magento\Rule\Model\Condition\Context;

/**
 * Class Address
 *
 * @package Licentia\Equity\Model\Segments\Condition
 */
class Address extends AbstractCondition
{

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected \Magento\SalesRule\Model\RuleFactory $ruleFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory
     */
    protected \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $addressCollection;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory
     */
    protected \Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory $paymentCollection;

    /**
     * @var \Magento\Directory\Model\Config\Source\CountryFactory
     */
    protected \Magento\Directory\Model\Config\Source\CountryFactory $countryFactory;

    /**
     * @var \Magento\Directory\Model\Config\Source\Allregion
     */
    protected \Magento\Directory\Model\Config\Source\Allregion $regionFactory;

    /**
     * @var \Magento\Payment\Model\Config\Source\AllmethodsFactory
     */
    protected \Magento\Payment\Model\Config\Source\AllmethodsFactory $paymentFactory;

    /**
     * @var \Magento\Shipping\Model\Config\Source\AllmethodsFactory
     */
    protected \Magento\Shipping\Model\Config\Source\AllmethodsFactory $shippingFactory;

    /**
     * Address constructor.
     *
     * @param \Magento\SalesRule\Model\RuleFactory                               $ruleFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $addressCollection
     * @param \Licentia\Equity\Helper\Data                                       $pandaHelper
     * @param \Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory $paymentCollection
     * @param \Magento\Directory\Model\Config\Source\Allregion                   $regionFactory
     * @param \Magento\Payment\Model\Config\Source\AllmethodsFactory             $paymentFactory
     * @param \Magento\Shipping\Model\Config\Source\AllmethodsFactory            $shippingFactory
     * @param \Magento\Directory\Model\Config\Source\CountryFactory              $countryFactory
     * @param Context                                                            $context
     * @param \Magento\Backend\Helper\Data                                       $backendData
     * @param \Magento\Framework\Registry                                        $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface                 $scopeInterface
     * @param \Licentia\Equity\Model\ResourceModel\Segments                      $segmentsResource
     * @param \Magento\Catalog\Model\ProductFactory                              $productFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory     $productCollection
     * @param \Magento\Catalog\Model\CategoryFactory                             $categoryFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory         $quoteCollection
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory         $orderCollection
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory    $itemCollection
     * @param array                                                              $data
     */
    public function __construct(
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $addressCollection,
        \Licentia\Equity\Helper\Data $pandaHelper,
        \Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory $paymentCollection,
        \Magento\Directory\Model\Config\Source\Allregion $regionFactory,
        \Magento\Payment\Model\Config\Source\AllmethodsFactory $paymentFactory,
        \Magento\Shipping\Model\Config\Source\AllmethodsFactory $shippingFactory,
        \Magento\Directory\Model\Config\Source\CountryFactory $countryFactory,
        Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface,
        \Licentia\Equity\Model\ResourceModel\Segments $segmentsResource,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollection,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemCollection,
        array $data = []
    ) {

        $this->shippingFactory = $shippingFactory;
        $this->paymentFactory = $paymentFactory;
        $this->regionFactory = $regionFactory;
        $this->countryFactory = $countryFactory;
        $this->paymentCollection = $paymentCollection;
        $this->addressCollection = $addressCollection;
        $this->ruleFactory = $ruleFactory;

        parent::__construct(
            $context,
            $backendData,
            $pandaHelper,
            $registry,
            $scopeInterface,
            $segmentsResource,
            $productFactory,
            $productCollection,
            $categoryFactory,
            $quoteCollection,
            $orderCollection,
            $itemCollection,
            $data
        );
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {

        $attributes = [
            'faddress_base_subtotal'     => __('Previous Order - Subtotal'),
            'faddress_total_qty_ordered' => __('Previous Order - Total Items Quantity'),
            'faddress_weight'            => __('Previous Order - Total Weight'),
            'faddress_payment_method'    => __('Previous Order - Payment Method'),
            'faddress_shipping_method'   => __('Previous Order - Shipping Method'),
            'faddress_postcode'          => __('Previous Order - Billing Postcode'),
            'faddress_region'            => __('Previous Order - Billing Region'),
            'faddress_region_id'         => __('Previous Order - Billing State/Province'),
            'faddress_country_id'        => __('Previous Order - Billing Country'),
            'faddress_created_at'        => __('Previous Order - Purchase Date'),
            #'faddress_rule_id'           => __('Shopping Cart Promo Rule'),
        ];

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @return AbstractCondition
     */
    public function getAttributeElement()
    {

        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }

    /**
     * @return string
     */
    public function getInputType()
    {

        switch ($this->getAttribute()) {
            case 'faddress_base_subtotal':
            case 'faddress_weight':
            case 'faddress_total_qty_ordered':
                return 'numeric';

            case 'faddress_shipping_method':
            case 'faddress_rule_id':
            case 'faddress_payment_method':
            case 'faddress_country_id':
            case 'faddress_region_id':
                return 'select';
            case 'faddress_created_at':
                return 'date';
        }

        return 'string';
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {

        switch ($this->getAttribute()) {
            case 'faddress_shipping_method':
            case 'faddress_rule_id':
            case 'faddress_payment_method':
            case 'faddress_country_id':
            case 'faddress_region_id':
                return 'select';
            case 'faddress_created_at':
                return 'date';
        }

        return 'text';
    }

    /**
     * @return mixed
     */
    public function getValueSelectOptions()
    {

        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'faddress_rule_id':
                    $options = $this->getRuleArray();
                    break;
                case 'faddress_country_id':
                    $options = $this->countryFactory->create()->toOptionArray();
                    break;

                case 'faddress_region_id':
                    $options = $this->regionFactory->toOptionArray();
                    break;

                case 'faddress_shipping_method':
                    $options = $this->shippingFactory->create()->toOptionArray();
                    break;

                case 'faddress_payment_method':
                    $options = $this->paymentFactory->create()->toOptionArray();
                    break;

                default:
                    $options = [];
            }
            $this->setData('value_select_options', $options);
        }

        return $this->getData('value_select_options');
    }

    /**
     * Validate Address Rule Condition
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $object)
    {

        $resource = $object->getResource();

        if ($object->getData('customer_email')) {
            $object->setData('email', $object->getData('customer_email'));
        }
        if ($object->getData('email_meta')) {
            $object->setData('email', $object->getData('email_meta'));
        }

        $currentSegment = $this->registry->registry('panda_segment');

        $mType = '';
        if ($currentSegment) {
            $mType = $currentSegment->getType();
        }

        $resultData = $this->registry->registry('panda_segments_data');

        $dbAttrName = str_replace('faddress_', '', $this->getAttribute());

        if (in_array($dbAttrName, ['shipping_method', 'base_subtotal', 'created_at', 'total_qty_ordered', 'weight'])) {
            $model = $this->orderCollection->create()
                                           ->addAttributeToSelect($dbAttrName)
                                           ->setPageSize(1)
                                           ->addAttributeToFilter('state', \Magento\Sales\Model\Order::STATE_COMPLETE);

            if ($currentSegment) {
                $model->addAttributeToFilter('store_id', ['in' => $currentSegment->getStoreIds()]);
            }

            if ($mType == 'customers') {
                $model->addAttributeToFilter('customer_id', $object->getId());
            } else {
                $model->addAttributeToFilter('customer_email', $object->getEmail());
            }

            if ($this->translateOperator() == 'eq' && $dbAttrName == 'created_at') {
                $model->addFieldToFilter(
                    'updated_at',
                    [
                        'from' => rtrim($this->getValueParsed(), ' 00:00:00') . ' 00:00:00',
                        'to'   => rtrim($this->getValueParsed(), ' 00:00:00') . ' 23:59:59',
                    ]
                );
            } else {
                $model->addAttributeToFilter(
                    $dbAttrName,
                    [$this->translateOperator() => $this->getValueParsed()]
                );
            }
        } elseif (in_array($dbAttrName, ['payment_method'])) {
            $orders = $this->orderCollection->create()
                                            ->addAttributeToSelect('entity_id')
                                            ->addAttributeToFilter(
                                                'state',
                                                \Magento\Sales\Model\Order::STATE_COMPLETE
                                            );
            if ($currentSegment) {
                $orders->addAttributeToFilter('store_id', ['in' => $currentSegment->getStoreIds()]);
            }

            if ($mType == 'customers') {
                $orders->addAttributeToFilter('customer_id', $object->getId());
            } else {
                $orders->addAttributeToFilter('customer_email', $object->getEmail());
            }

            $ordersIds = [];
            foreach ($orders as $order) {
                $ordersIds[] = $order->getId();
            }
            $model = $this->paymentCollection->create()
                                             ->addAttributeToSelect('method')
                                             ->setPageSize(1)
                                             ->addAttributeToFilter('parent_id', ['in' => $ordersIds])
                                             ->addAttributeToFilter('method', $this->getValueParsed());
        } elseif (in_array($dbAttrName, ['rule_id'])) {
            if ($this->registry->registry('panda_rule_' . $this->getValueParsed())) {
                $rule = $this->registry->registry('panda_rule_' . $this->getValueParsed());
            } else {
                $rule = $this->ruleFactory->create()->load($this->getValueParsed());
                $this->registry->register('panda_rule_' . $this->getValueParsed(), $rule);
            }

            if (!$rule->getId()) {
                return false;
            }

            $total = $rule->getResource()->getCustomerUses($rule, $object->getId());
            $resultData->setData((string) $this->getAttributeName(), $total);
            $object->setData($this->getAttribute(), $total > 0 ? true : false);

            return parent::validate($object);
        } else {
            $model = $this->addressCollection->create()
                                             ->addAttributeToSelect($dbAttrName)
                                             ->setPageSize(1)
                                             ->addAttributeToFilter(
                                                 'main_table.' . $dbAttrName,
                                                 [$this->translateOperator() => $this->getValueParsed()]
                                             );

            $model->getSelect()
                  ->join(
                      $resource->getTable('sales_order'),
                      $resource->getTable('sales_order') . '.entity_id = main_table.parent_id',
                      []
                  );

            $model->getSelect()
                  ->where($resource->getTable('sales_order') . '.customer_email=?', $object->getEmail())
                  ->where('address_type=?', 'billing');
        }

        if ($model->count() == 0) {
            return false;
        } else {
            switch ($this->getAttribute()) {
                case 'faddress_country_id':
                    $resultData->setData('type_' . (string) $this->getAttributeName(), 'options');
                    break;
                case 'faddress_region_id':
                    $resultData->setData('type_' . (string) $this->getAttributeName(), 'options');
                    break;
                case 'faddress_shipping_method':
                    $resultData->setData('type_' . (string) $this->getAttributeName(), 'options');
                    break;
                case 'faddress_payment_method':
                    $resultData->setData('type_' . (string) $this->getAttributeName(), 'options');
                    break;
                default:
                    break;
            }

            $object->setData($this->getAttribute(), $this->getValueParsed());
        }

        $item = $model->getFirstItem()->getData();

        $item = array_values($item);

        $resultFinal = isset($item[0]) ? $item[0] : '';
        $resultData->setData((string) $this->getAttributeName(), $resultFinal);

        return true;
    }

    /**
     * @return $this
     */
    public function collectValidatedAttributes()
    {

        $attribute = $this->getAttribute();

        $attributes = $this->getRule()->getCollectedAttributes();
        $attributes[$attribute] = true;
        $this->getRule()->setCollectedAttributes($attributes);

        return $this;
    }

    /**
     * @return string
     */
    public function translateOperator()
    {

        $operator = $this->getOperator();

        $newValue = [
            '=='  => 'eq',
            '!='  => 'neq',
            '>='  => 'gteq',
            '<='  => 'lteq',
            '>'   => 'gt',
            '<'   => 'lt',
            '{}'  => 'like',
            '!{}' => 'nlike',
            '()'  => 'in',
            '!()' => 'nin',
        ];

        if (isset($newValue[$operator])) {
            return $newValue[$operator];
        }

        return 'eq';
    }

    /**
     * @return array
     */
    public function getRuleArray()
    {

        $collection = $this->ruleFactory->create()->getCollection();

        $return = [];
        foreach ($collection as $rule) {
            $return[$rule->getId()] = $rule->getName();
        }

        return $return;
    }
}
