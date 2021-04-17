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

namespace Licentia\Equity\Model;

use Licentia\Equity\Api\Data\KpisInterfaceFactory;
use Licentia\Equity\Api\Data\KpisSearchResultsInterfaceFactory;
use Licentia\Equity\Api\KpisRepositoryInterface;
use Licentia\Equity\Model\ResourceModel\Kpis as ResourceKpis;
use Licentia\Equity\Model\ResourceModel\Kpis\CollectionFactory as KpisCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class KpisRepository
 *
 * @package Licentia\Panda\Model
 */
class KpisRepository implements KpisRepositoryInterface
{

    /**
     * @var DataObjectHelper
     */
    protected DataObjectHelper $dataObjectHelper;

    /**
     * @var
     */
    protected $KpisFactory;

    /**
     * @var KpisSearchResultsInterfaceFactory
     */
    protected KpisSearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * @var KpisInterfaceFactory
     */
    protected KpisInterfaceFactory $dataKpisFactory;

    /**
     * @var KpisFactory
     */
    protected KpisFactory $kpisFactory;

    /**
     * @var KpisCollectionFactory
     */
    protected KpisCollectionFactory $kpisCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var ResourceKpis
     */
    protected ResourceKpis $resource;

    /**
     * @var DataObjectProcessor
     */
    protected DataObjectProcessor $dataObjectProcessor;

    /**
     * @var
     */
    protected $KpisCollectionFactory;

    /**
     * @param ResourceKpis                      $resource
     * @param KpisFactory                       $kpisFactory
     * @param KpisInterfaceFactory              $dataKpisFactory
     * @param KpisCollectionFactory             $kpisCollectionFactory
     * @param KpisSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper                  $dataObjectHelper
     * @param DataObjectProcessor               $dataObjectProcessor
     * @param StoreManagerInterface             $storeManager
     */
    public function __construct(
        ResourceKpis $resource,
        KpisFactory $kpisFactory,
        KpisInterfaceFactory $dataKpisFactory,
        KpisCollectionFactory $kpisCollectionFactory,
        KpisSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {

        $this->resource = $resource;
        $this->kpisFactory = $kpisFactory;
        $this->kpisCollectionFactory = $kpisCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataKpisFactory = $dataKpisFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * returns list
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     *
     * @return \Licentia\Equity\Api\Data\KpisSearchResultsInterface
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->kpisCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $items = [];

        foreach ($collection as $kpisModel) {
            $kpisData = $this->dataKpisFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $kpisData,
                $kpisModel->getData(),
                \Licentia\Equity\Api\Data\KpisInterface::class
            );
            $items[] = $this->dataObjectProcessor->buildOutputDataArray(
                $kpisData,
                \Licentia\Equity\Api\Data\KpisInterface::class
            );
        }
        $searchResults->setItems($items);

        return $searchResults;
    }
}
