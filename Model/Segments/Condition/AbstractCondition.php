<?php

/**
 * Copyright (C) 2020 Licentia, Unipessoal LDA
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @title      Licentia Panda - Magento® Sales Automation Extension
 * @package    Licentia
 * @author     Bento Vilas Boas <bento@licentia.pt>
 * @copyright  Copyright (c) Licentia - https://licentia.pt
 * @license    GNU General Public License V3
 * @modified   29/01/20, 15:22 GMT
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
    protected $quoteCollection;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollection;

    /**
     * @var \Licentia\Equity\Model\ResourceModel\Segments
     */
    protected $resource;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * Adminhtml data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendData;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory
     */
    protected $itemCollection;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productCollection;

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected $pandaHelper;

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
