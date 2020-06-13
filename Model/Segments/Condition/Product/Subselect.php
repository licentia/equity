<?php
/**
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

namespace Licentia\Equity\Model\Segments\Condition\Product;

/**
 * Class Subselect
 *
 * @package Licentia\Equity\Model\Segments\Condition\Product
 */
class Subselect extends Combine
{

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollection;

    /**
     * Subselect constructor.
     *
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory
     * @param \Magento\Rule\Model\Condition\Context                      $context
     * @param \Magento\SalesRule\Model\Rule\Condition\Product            $ruleConditionProduct
     * @param array                                                      $data
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\SalesRule\Model\Rule\Condition\Product $ruleConditionProduct,
        array $data = []
    ) {

        parent::__construct($context, $ruleConditionProduct, $data);
        $this->setType(Subselect::class)->setValue(null);

        $this->orderCollection = $collectionFactory;
    }

    /**
     * Load array
     *
     * @param array  $arr
     * @param string $key
     *
     * @return $this
     */
    public function loadArray($arr, $key = 'conditions')
    {

        $this->setAttribute($arr['attribute']);
        $this->setOperator($arr['operator']);
        parent::loadArray($arr, $key);

        return $this;
    }

    /**
     * Return as xml
     *
     * @param string $containerKey
     * @param string $itemKey
     *
     * @return string
     */
    public function asXml($containerKey = 'conditions', $itemKey = 'condition')
    {

        $xml = '<attribute>' .
               $this->getAttribute() .
               '</attribute>' .
               '<operator>' .
               $this->getOperator() .
               '</operator>' .
               parent::asXml(
                   $containerKey,
                   $itemKey
               );

        return $xml;
    }

    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {

        $this->setAttributeOption(
            [
                'qty_ordered'    => __('total quantity'),
                'base_row_total' => __('total amount'),
            ]
        );

        return $this;
    }

    /**
     * Load value options
     *
     * @return $this
     */
    public function loadValueOptions()
    {

        return $this;
    }

    /**
     * Load operator options
     *
     * @return $this
     */
    public function loadOperatorOptions()
    {

        $this->setOperatorOption(
            [
                '=='  => __('is'),
                '!='  => __('is not'),
                '>='  => __('equals or greater than'),
                '<='  => __('equals or less than'),
                '>'   => __('greater than'),
                '<'   => __('less than'),
                '()'  => __('is one of'),
                '!()' => __('is not one of'),
            ]
        );

        return $this;
    }

    /**
     * Get value element type
     *
     * @return string
     */
    public function getValueElementType()
    {

        return 'text';
    }

    /**
     * Return as html
     *
     * @return string
     */
    public function asHtml()
    {

        $html = $this->getTypeElement()->getHtml() . __(
                "If %1 %2 %3 for a subselection of items in cart matching %4 of these conditions:",
                $this->getAttributeElement()->getHtml(),
                $this->getOperatorElement()->getHtml(),
                $this->getValueElement()->getHtml(),
                $this->getAggregatorElement()->getHtml()
            );
        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }

        return $html;
    }

    /**
     * Validate
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     *
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {

        if (!$this->getConditions()) {
            return false;
        }
        $attr = $this->getAttribute();

        $orders = $this->orderCollection->create();

        if ($model->getCustomerId()) {
            $orders->getSelect()->where(
                'customer_email=? OR customer_id=' . $model->getCustomerId(),
                $model->getEmail()
            );
        } else {
            $orders->getSelect()->where('customer_email=? ', $model->getEmail());
        }

        /** @var \Magento\Sales\Model\Order $order */
        foreach ($orders as $order) {
            try {
                $total = 0;
                foreach ($order->getAllItems() as $item) {
                    if (parent::validate($item)) {
                        $total += $item->getData($attr);
                    }
                }

                $result = $this->validateAttribute($total);

                if ($result) {
                    return true;
                }
            } catch (\Exception $e) {
            }
        }

        return false;
    }
}
