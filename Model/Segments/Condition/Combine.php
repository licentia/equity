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
 * Class Combine
 *
 * @package Licentia\Equity\Model\Segments\Condition
 */
class Combine extends \Magento\Rule\Model\Condition\Combine
{

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var ActivityFactory
     */
    protected $activityFactory;

    /**
     * @var AddressFactory
     */
    protected $addressFactory;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var SkuFactory
     */
    protected $skuFactory;

    /**
     * @var SubscriberFactory
     */
    protected $subscribersFactory;

    /**
     * @var SearchFactory
     */
    protected $searchFactory;

    /**
     * @var DefaultAddressFactory
     */
    protected $defaultAddressFactory;

    /**
     * @param \Magento\Framework\Registry  $registry
     * @param \Licentia\Equity\Helper\Data $pandaHelper
     * @param ActivityFactory              $activityFactory
     * @param DefaultAddressFactory        $defaultAddressFactory
     * @param AddressFactory               $addressFactory
     * @param CustomerFactory              $customerFactory
     * @param SkuFactory                   $skuFactory
     * @param SearchFactory                $searchFactory
     * @param SubscriberFactory            $subscribersFactory
     * @param Context                      $context
     * @param array                        $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Licentia\Equity\Helper\Data $pandaHelper,
        ActivityFactory $activityFactory,
        DefaultAddressFactory $defaultAddressFactory,
        AddressFactory $addressFactory,
        CustomerFactory $customerFactory,
        SkuFactory $skuFactory,
        SearchFactory $searchFactory,
        SubscriberFactory $subscribersFactory,
        Context $context,
        array $data = []
    ) {

        parent::__construct($context, $data);

        $this->setType('Licentia\Equity\Model\Segments\Condition\Combine');

        $this->activityFactory = $activityFactory;
        $this->defaultAddressFactory = $defaultAddressFactory;
        $this->addressFactory = $addressFactory;
        $this->customerFactory = $customerFactory;
        $this->skuFactory = $skuFactory;
        $this->pandaHelper = $pandaHelper;
        $this->subscribersFactory = $subscribersFactory;
        $this->searchFactory = $searchFactory;
        $this->registry = $registry;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getNewChildSelectOptions()
    {

        $current = $this->registry->registry("panda_segment");

        $customerCondition = $this->customerFactory->create();
        $customerAttributes = $customerCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = [];
        foreach ($customerAttributes as $code => $label) {
            $attributes[] = [
                'value' => 'Licentia\Equity\Model\Segments\Condition\Customer|' . $code,
                'label' => $label,
            ];
        }

        $conditions = parent::getNewChildSelectOptions();

        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => 'Licentia\Equity\Model\Segments\Condition\Combine',
                    'label' => __('Conditions combination'),
                ],
            ]
        );

        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => \Licentia\Equity\Model\Segments\Condition\Product\Found::class,
                    'label' => __('Previous Order Product attribute combination'),
                ],
                [
                    'value' => \Licentia\Equity\Model\Segments\Condition\Product\Subselect::class,
                    'label' => __('Previous Order Products subselection'),
                ],
            ]
        );

        $addressCondition = $this->addressFactory->create();
        $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();
        $attributesCart = [];
        foreach ($addressAttributes as $code => $label) {
            if ($code == 'faddress_rule_id' && $current && $current->getType() != 'customers') {
                continue;
            }

            $attributesCart[] = [
                'value' => 'Licentia\Equity\Model\Segments\Condition\Address|' . $code,
                'label' => $label,
            ];
        }

        $addressActivity = $this->activityFactory->create();
        $activityAttributes = $addressActivity->loadAttributeOptions()->getAttributeOption();
        $attributesActivity = [];
        foreach ($activityAttributes as $code => $label) {
            $attributesActivity[] = [
                'value' => 'Licentia\Equity\Model\Segments\Condition\Activity|' . $code,
                'label' => $label,
            ];
        }

        $productCondition = $this->skuFactory->create();
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();
        $pAttributes = [];
        foreach ($productAttributes as $code => $label) {
            $pAttributes[] = [
                'value' => 'Licentia\Equity\Model\Segments\Condition\Sku|' . $code,
                'label' => $label,
            ];
        }

        /** @var Subscriber $subscriber */
        $subscriber = $this->subscribersFactory->create();

        $cartAttributes = $subscriber->loadAttributeOptions()->getAttributeOption();
        $cAttributes = [];
        foreach ($cartAttributes as $code => $label) {
            $cAttributes[] = [
                'value' => 'Licentia\Equity\Model\Segments\Condition\Subscriber|' . $code,
                'label' => $label,
            ];
        }

        $conditions = array_merge_recursive(
            $conditions,
            [
                ['label' => __('Subscriber Account'), 'value' => $cAttributes],
            ]
        );

        $searches = $this->searchFactory->create();
        $searchAttributes = $searches->loadAttributeOptions()->getAttributeOption();
        $cAttributes = [];
        foreach ($searchAttributes as $code => $label) {
            $cAttributes[] = [
                'value' => 'Licentia\Equity\Model\Segments\Condition\Search|' . $code,
                'label' => $label,
            ];
        }
        $conditions = array_merge_recursive(
            $conditions,
            [
                ['label' => __('Customer Searches'), 'value' => $cAttributes],
            ]
        );

        if ($current && $current->getType() == 'customers') {
            $conditions = array_merge_recursive(
                $conditions,
                [
                    ['label' => __('Customer Attribute'), 'value' => $attributes],
                ]
            );
        }

        $conditions = array_merge_recursive(
            $conditions,
            [
                ['label' => __('Customer Activity'), 'value' => $attributesActivity],
            ]
        );

        $conditions = array_merge_recursive(
            $conditions,
            [
                ['label' => __('Previous Order - Cart Attribute'), 'value' => $attributesCart],
                ['label' => __('Previous Order - SKU'), 'value' => $pAttributes],
            ]
        );

        if ($current && $current->getType() == 'customers') {
            $defaultAddressCondition = $this->defaultAddressFactory->create();
            $defaultAddressAttributes = $defaultAddressCondition->loadAttributeOptions()->getAttributeOption();
            $attributesDefaultAddress = [];
            foreach ($defaultAddressAttributes as $code => $label) {
                $attributesDefaultAddress[] = [
                    'value' => 'Licentia\Equity\Model\Segments\Condition\DefaultAddress|' . $code,
                    'label' => $label,
                ];
            }

            $conditions = array_merge_recursive(
                $conditions,
                [
                    ['label' => __('Customer Default Billing Address'), 'value' => $attributesDefaultAddress],
                ]
            );
        }

        return $conditions;
    }

    /**
     * @param $productCollection
     *
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {

        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }

        return $this;
    }
}
