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
 * Class AbstractCondition
 *
 * @package Licentia\Equity\Model\Segments\Condition
 */
class AbstractCondition extends \Magento\Rule\Model\Condition\AbstractCondition
{

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory
     */
    protected \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollection;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection;

    /**
     * @var \Licentia\Equity\Model\ResourceModel\Segments
     */
    protected \Licentia\Equity\Model\ResourceModel\Segments $resource;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    protected \Magento\Framework\Registry $registry;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected \Magento\Catalog\Model\CategoryFactory $categoryFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected \Magento\Catalog\Model\ProductFactory $productFactory;

    /**
     * Adminhtml data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected \Magento\Backend\Helper\Data $backendData;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory
     */
    protected \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemCollection;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected \Magento\Catalog\Model\ProductFactory $productCollection;

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected \Licentia\Equity\Helper\Data $pandaHelper;

    /**
     * @param Context                                                         $context
     * @param \Magento\Backend\Helper\Data                                    $backendData
     * @param \Licentia\Equity\Helper\Data                                    $pandaHelper
     * @param \Magento\Framework\Registry                                     $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface              $scopeInterface
     * @param \Licentia\Equity\Model\ResourceModel\Segments                   $segmentsResource
     * @param \Magento\Catalog\Model\ProductFactory                           $productFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory  $productCollection
     * @param \Magento\Catalog\Model\CategoryFactory                          $categoryFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory      $quoteCollection
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory      $orderCollection
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemCollection
     * @param array                                                           $data
     */
    public function __construct(
        Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Licentia\Equity\Helper\Data $pandaHelper,
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

        parent::__construct($context, $data);

        $this->registry = $registry;
        $this->scopeConfig = $scopeInterface;
        $this->resource = $segmentsResource;
        $this->orderCollection = $orderCollection;
        $this->quoteCollection = $quoteCollection;
        $this->categoryFactory = $categoryFactory;
        $this->productFactory = $productFactory;
        $this->backendData = $backendData;
        $this->itemCollection = $itemCollection;
        $this->productCollection = $productFactory;
        $this->pandaHelper = $pandaHelper;
    }
}
