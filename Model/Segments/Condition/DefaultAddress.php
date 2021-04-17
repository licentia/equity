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
 * Class DefaultAddress
 *
 * @package Licentia\Equity\Model\Segments\Condition
 */
class DefaultAddress extends AbstractCondition
{

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory
     */
    protected $addressCollection;

    /**
     * @var \Magento\Directory\Model\Config\Source\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var \Magento\Directory\Model\Config\Source\Allregion
     */
    protected $regionFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @param \Magento\Customer\Model\CustomerFactory                                                $customerFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory                     $addressCollection
     * @param \Licentia\Equity\Helper\Data                                                           $pandaHelper
     * @param \Magento\Directory\Model\Config\Source\Allregion                                       $regionFactory
     * @param \Magento\Directory\Model\Config\Source\CountryFactory                                  $countryFactory
     * @param Context                                                                                $context
     * @param \Magento\Backend\Helper\Data                                                           $backendData
     * @param \Magento\Framework\Registry                                                            $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface|\Magento\Store\Model\ScopeInterface $scopeInterface
     * @param \Licentia\Equity\Model\ResourceModel\Segments                                          $segmentsResource
     * @param \Magento\Catalog\Model\ProductFactory                                                  $productFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory                         $productCollection
     * @param \Magento\Catalog\Model\CategoryFactory                                                 $categoryFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory                             $quoteCollection
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory                             $orderCollection
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory                        $itemCollection
     * @param array                                                                                  $data
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $addressCollection,
        \Licentia\Equity\Helper\Data $pandaHelper,
        \Magento\Directory\Model\Config\Source\Allregion $regionFactory,
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

        $this->customerFactory = $customerFactory;
        $this->regionFactory = $regionFactory;
        $this->countryFactory = $countryFactory;
        $this->addressCollection = $addressCollection;

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
            'fdefaultaddress_postcode'   => __('Default Billing Address - Postcode'),
            'fdefaultaddress_region'     => __('Default Billing Address - Region'),
            'fdefaultaddress_region_id'  => __('Default Billing Address - State/Province'),
            'fdefaultaddress_country_id' => __('Default Billing Address - Country'),
        ];

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @return $this
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
            case 'fdefaultaddress_country_id':
            case 'fdefaultaddress_region_id':
                return 'select';
        }

        return 'string';
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {

        switch ($this->getAttribute()) {
            case 'fdefaultaddress_country_id':
            case 'fdefaultaddress_region_id':
                return 'select';
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
                case 'fdefaultaddress_country_id':
                    $options = $this->countryFactory->create()->toOptionArray();
                    break;

                case 'fdefaultaddress_region_id':
                    $options = $this->regionFactory->toOptionArray();
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
     * @param \Magento\Framework\Model\AbstractModel $model
     *
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {

        if ($model->getData('customer_email')) {
            $model->setData('email', $model->getData('customer_email'));
        }
        if ($model->getData('email_meta')) {
            $model->setData('email', $model->getData('email_meta'));
        }

        $customer = $this->customerFactory->create()->loadByEmail($model->getData('email'));

        if (!$customer->getId() || !$customer->getDefaultShippingAddress()) {
            return false;
        }

        $resultData = $this->registry->registry('panda_segments_data');

        $dbAttrName = str_replace('fdefaultaddress_', '', $this->getAttribute());

        $model->setData(
            $this->getAttribute(),
            $customer->getDefaultShippingAddress()
                     ->getData($dbAttrName)
        );

        if ($resultData) {
            $resultData->setData((string) $this->getAttributeName(), $model->getData($this->getAttribute()));
        }

        return parent::validate($model);
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
}
