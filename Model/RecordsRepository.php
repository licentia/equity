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

use Licentia\Equity\Api\Data\RecordsInterfaceFactory;
use Licentia\Equity\Api\Data\RecordsSearchResultsInterfaceFactory;
use Licentia\Equity\Api\RecordsRepositoryInterface;
use \Licentia\Equity\Model\ResourceModel\Records as ResourceRecords;
use \Licentia\Equity\Model\ResourceModel\Records\CollectionFactory as RecordsCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class RecordsRepository
 *
 * @package Licentia\Panda\Model
 */
class RecordsRepository implements RecordsRepositoryInterface
{

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var RecordsInterfaceFactory
     */
    protected $dataRecordsFactory;

    /**
     * @var
     */
    protected $RecordsFactory;

    /**
     * @var RecordsSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var RecordsFactory
     */
    protected $recordsFactory;

    /**
     * @var RecordsCollectionFactory
     */
    protected $recordsCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ResourceRecords
     */
    protected $resource;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var
     */
    protected $RecordsCollectionFactory;

    /**
     * @param ResourceRecords                      $resource
     * @param RecordsFactory                       $recordsFactory
     * @param RecordsInterfaceFactory              $dataRecordsFactory
     * @param RecordsCollectionFactory             $recordsCollectionFactory
     * @param RecordsSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper                     $dataObjectHelper
     * @param DataObjectProcessor                  $dataObjectProcessor
     * @param StoreManagerInterface                $storeManager
     */
    public function __construct(
        ResourceRecords $resource,
        RecordsFactory $recordsFactory,
        RecordsInterfaceFactory $dataRecordsFactory,
        RecordsCollectionFactory $recordsCollectionFactory,
        RecordsSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {

        $this->resource = $resource;
        $this->recordsFactory = $recordsFactory;
        $this->recordsCollectionFactory = $recordsCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataRecordsFactory = $dataRecordsFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Licentia\Equity\Api\Data\RecordsInterface $records
    ) {

        /* if (empty($records->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $records->setStoreId($storeId);
        } */
        try {
            $this->resource->save($records);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the records: %1',
                    $exception->getMessage()
                )
            );
        }

        return $records;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($recordsId)
    {

        $records = $this->recordsFactory->create();
        $records->load($recordsId);
        if (!$records->getId()) {
            throw new NoSuchEntityException(__('Records with id "%1" does not exist.', $recordsId));
        }

        return $records;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->recordsCollectionFactory->create();
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

        foreach ($collection as $recordsModel) {
            $recordsData = $this->dataRecordsFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $recordsData,
                $recordsModel->getData(),
                \Licentia\Equity\Api\Data\RecordsInterface::class
            );
            $items[] = $this->dataObjectProcessor->buildOutputDataArray(
                $recordsData,
                \Licentia\Equity\Api\Data\RecordsInterface::class
            );
        }
        $searchResults->setItems($items);

        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Licentia\Equity\Api\Data\RecordsInterface $records
    ) {

        try {
            $this->resource->delete($records);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete the Records: %1',
                    $exception->getMessage()
                )
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($recordsId)
    {

        return $this->delete($this->getById($recordsId));
    }
}
