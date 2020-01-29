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

namespace Licentia\Equity\Model;

use Licentia\Equity\Api\Data\SegmentsInterfaceFactory;
use Licentia\Equity\Api\Data\SegmentsSearchResultsInterfaceFactory;
use Licentia\Equity\Api\SegmentsRepositoryInterface;
use Licentia\Equity\Model\ResourceModel\Segments as ResourceSegments;
use Licentia\Equity\Model\ResourceModel\Segments\CollectionFactory as SegmentsCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class SegmentsRepository
 *
 * @package Licentia\Panda\Model
 */
class SegmentsRepository implements SegmentsRepositoryInterface
{

    /**
     * @var
     */
    protected $SegmentsCollectionFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var SegmentsSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var SegmentsFactory
     */
    protected $segmentsFactory;

    /**
     * @var SegmentsCollectionFactory
     */
    protected $segmentsCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var SegmentsInterfaceFactory
     */
    protected $dataSegmentsFactory;

    /**
     * @var ResourceSegments
     */
    protected $resource;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var
     */
    protected $SegmentsFactory;

    /**
     * @param ResourceSegments                      $resource
     * @param SegmentsFactory                       $segmentsFactory
     * @param SegmentsInterfaceFactory              $dataSegmentsFactory
     * @param SegmentsCollectionFactory             $segmentsCollectionFactory
     * @param SegmentsSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper                      $dataObjectHelper
     * @param DataObjectProcessor                   $dataObjectProcessor
     * @param StoreManagerInterface                 $storeManager
     */
    public function __construct(
        ResourceSegments $resource,
        SegmentsFactory $segmentsFactory,
        SegmentsInterfaceFactory $dataSegmentsFactory,
        SegmentsCollectionFactory $segmentsCollectionFactory,
        SegmentsSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {

        $this->resource = $resource;
        $this->segmentsFactory = $segmentsFactory;
        $this->segmentsCollectionFactory = $segmentsCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataSegmentsFactory = $dataSegmentsFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Licentia\Equity\Api\Data\SegmentsInterface $segments
    ) {

        /* if (empty($segments->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $segments->setStoreId($storeId);
        } */
        try {
            $this->resource->save($segments);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the segments: %1',
                    $exception->getMessage()
                )
            );
        }

        return $segments;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($segmentsId)
    {

        $segments = $this->segmentsFactory->create();
        $segments->load($segmentsId);
        if (!$segments->getId()) {
            throw new NoSuchEntityException(__('Segments with id "%1" does not exist.', $segmentsId));
        }

        return $segments;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->segmentsCollectionFactory->create();
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

        foreach ($collection as $segmentsModel) {
            $segmentsData = $this->dataSegmentsFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $segmentsData,
                $segmentsModel->getData(),
                \Licentia\Equity\Api\Data\SegmentsInterface::class
            );
            $items[] = $this->dataObjectProcessor->buildOutputDataArray(
                $segmentsData,
                \Licentia\Equity\Api\Data\SegmentsInterface::class
            );
        }
        $searchResults->setItems($items);

        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Licentia\Equity\Api\Data\SegmentsInterface $segments
    ) {

        try {
            $this->resource->delete($segments);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete the Segments: %1',
                    $exception->getMessage()
                )
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($segmentsId)
    {

        return $this->delete($this->getById($segmentsId));
    }
}
