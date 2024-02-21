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
 * Class Customer
 *
 * @package Licentia\Equity\Model\Segments\Condition
 */
class Customer extends AbstractCondition
{

    /**
     * @var null
     */
    protected $entityAttributeValues = null;

    /**
     * @var
     */
    protected $customerFactory;

    /**
     * @var
     */
    protected $eavConfig;

    /**
     * @var \Magento\Customer\Model\ResourceModel\CustomerFactory
     */
    protected $customerResource;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param Context                                                         $context
     * @param \Magento\Customer\Model\Session                                 $customerSession
     * @param \Licentia\Equity\Helper\Data                                    $pandaHelper
     * @param \Magento\Backend\Helper\Data                                    $backendData
     * @param \Magento\Framework\Registry                                     $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface              $scopeInterface
     * @param \Licentia\Equity\Model\ResourceModel\Segments                   $segmentsResource
     * @param \Magento\Catalog\Model\ProductFactory                           $productFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory  $productCollection
     * @param \Magento\Catalog\Model\CategoryFactory                          $categoryFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory      $quoteCollection
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory      $orderCollection
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemCollection
     * @param \Magento\Eav\Model\Config                                       $eavConfig
     * @param \Magento\Customer\Model\CustomerFactory                         $customerFactory
     * @param \Magento\Customer\Model\ResourceModel\CustomerFactory           $customerResource
     * @param array                                                           $data
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Licentia\Equity\Helper\Data $pandaHelper,
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
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\ResourceModel\CustomerFactory $customerResource,
        array $data = []
    ) {

        $this->customerFactory = $customerFactory;
        $this->customerResource = $customerResource;
        $this->eavConfig = $eavConfig;
        $this->customerSession = $customerSession;

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
     * Retrieve attribute object
     *
     */
    public function getAttributeObject()
    {

        $obj = $this->eavConfig
            ->getAttribute('customer', $this->getAttribute());

        return $obj;
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {

        $productAttributes = $this->customerResource->create()->loadAllAttributes()->getAttributesByCode();

        $attrToRemove = [
            'increment_id',
            'created_at',
            'gender',
            'updated_at',
            'attribute_set_id',
            'entity_type_id',
            'entity_id',
            'website_id',
            'confirmation',
            'created_in',
            'default_billing',
            'default_shipping',
            'disable_auto_group_change',
            'lock_expires',
            'failures_num',
            'first_failure',
            'password_hash',
        ];

        $attributes = [];

        foreach ($productAttributes as $attribute) {
            if (in_array($attribute->getAttributeCode(), $attrToRemove)) {
                continue;
            }

            if ($attribute->getFrontendLabel() && strlen($attribute->getFrontendLabel()) == 0) {
                continue;
            }

            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareValueOptions()
    {

        $selectReady = $this->getData('value_select_options');
        $hashedReady = $this->getData('value_option');
        if ($selectReady && $hashedReady) {
            return $this;
        }

        $selectOptions = null;
        if (is_object($this->getAttributeObject())) {
            $attributeObject = $this->getAttributeObject();
            if ($attributeObject->usesSource()) {
                $selectOptions = $attributeObject->getSource()->getAllOptions();
            }
        }

        if ($selectOptions !== null) {
            if (!$selectReady) {
                $this->setData('value_select_options', $selectOptions);
            }
            if (!$hashedReady) {
                $hashedOptions = [];
                foreach ($selectOptions as $o) {
                    if (is_array($o['value'])) {
                        continue; // We cannot use array as index
                    }
                    $hashedOptions[$o['value']] = $o['label'];
                }
                $this->setData('value_option', $hashedOptions);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValueSelectOptions()
    {

        $this->_prepareValueOptions();

        return $this->getData('value_select_options');
    }

    /**
     * @param $customerCollection
     *
     * @return $this
     */
    public function collectValidatedAttributes($customerCollection)
    {

        $attribute = $this->getAttribute();

        $attributes = $this->getRule()->getCollectedAttributes();
        $attributes[$attribute] = true;
        $this->getRule()->setCollectedAttributes($attributes);

        $customerCollection->addAttributeToSelect($attribute, 'left');

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

        if (!is_object($this->getAttributeObject())) {
            return 'string';
        }
        switch ($this->getAttributeObject()
                     ->getFrontendInput()) {
            case 'select':
                return 'select';

            case 'multiselect':
                return 'multiselect';

            case 'date':
                return 'date';

            case 'boolean':
                return 'boolean';

            default:
                return 'string';
        }
    }

    /**
     * @param null $option
     *
     * @return mixed
     */
    public function getValueOption($option = null)
    {

        $this->_prepareValueOptions();

        return $this->getData('value_option' . (!is_null($option) ? '/' . $option : ''));
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $model
     *
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {

        $resultData = $this->registry->registry('panda_segments_data');
        if (!$resultData) {
            $customer = $this->customerSession;

            if (!$customer->getCustomerId()) {
                return false;
            }

            $model->setData('id', $customer->getCustomerId());
        }

        if (null === $model->getData($this->getAttribute())) {
            $model = $this->customerFactory->create()->load($model->getId());
        }

        if ($resultData) {
            $resultData->setData((string) $this->getAttributeName(), $model->getData($this->getAttribute()));
        }

        return parent::validate($model);
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {

        if (!is_object($this->getAttributeObject())) {
            return 'text';
        }
        switch ($this->getAttributeObject()
                     ->getFrontendInput()) {
            case 'select':
            case 'boolean':
                return 'select';

            case 'multiselect':
                return 'multiselect';

            case 'date':
                return 'date';

            default:
                return 'text';
        }
    }
}
