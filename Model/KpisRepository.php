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
    protected $dataObjectHelper;

    /**
     * @var
     */
    protected $KpisFactory;

    /**
     * @var KpisSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var KpisInterfaceFactory
     */
    protected $dataKpisFactory;

    /**
     * @var KpisFactory
     */
    protected $kpisFactory;

    /**
     * @var KpisCollectionFactory
     */
    protected $kpisCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ResourceKpis
     */
    protected $resource;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

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
