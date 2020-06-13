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

namespace Licentia\Equity\Model;

use Licentia\Equity\Api\Data\FormulasInterfaceFactory;
use Licentia\Equity\Api\Data\FormulasSearchResultsInterfaceFactory;
use Licentia\Equity\Api\FormulasRepositoryInterface;
use Licentia\Equity\Model\ResourceModel\Formulas as ResourceFormulas;
use Licentia\Equity\Model\ResourceModel\Formulas\CollectionFactory as FormulasCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class FormulasRepository
 *
 * @package Licentia\Panda\Model
 */
class FormulasRepository implements FormulasRepositoryInterface
{

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var FormulasSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var FormulasFactory
     */
    protected $formulasFactory;

    /**
     * @var FormulasCollectionFactory
     */
    protected $formulasCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ResourceFormulas
     */
    protected $resource;

    /**
     * @var
     */
    protected $FormulasCollectionFactory;

    /**
     * @var
     */
    protected $FormulasFactory;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var FormulasInterfaceFactory
     */
    protected $dataFormulasFactory;

    /**
     * @param ResourceFormulas                      $resource
     * @param FormulasFactory                       $formulasFactory
     * @param FormulasInterfaceFactory              $dataFormulasFactory
     * @param FormulasCollectionFactory             $formulasCollectionFactory
     * @param FormulasSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper                      $dataObjectHelper
     * @param DataObjectProcessor                   $dataObjectProcessor
     * @param StoreManagerInterface                 $storeManager
     */
    public function __construct(
        ResourceFormulas $resource,
        FormulasFactory $formulasFactory,
        FormulasInterfaceFactory $dataFormulasFactory,
        FormulasCollectionFactory $formulasCollectionFactory,
        FormulasSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {

        $this->resource = $resource;
        $this->formulasFactory = $formulasFactory;
        $this->formulasCollectionFactory = $formulasCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataFormulasFactory = $dataFormulasFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     * @throws CouldNotSaveException
     */
    public function save(
        \Licentia\Equity\Api\Data\FormulasInterface $formulas
    ) {

        try {
            $this->resource->save($formulas);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the formulas: %1',
                    $exception->getMessage()
                )
            );
        }

        return $formulas;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($formulasId)
    {

        $formulas = $this->formulasFactory->create();
        $formulas->load($formulasId);
        if (!$formulas->getId()) {
            throw new NoSuchEntityException(__('Formulas with id "%1" does not exist.', $formulasId));
        }

        return $formulas;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->formulasCollectionFactory->create();
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

        foreach ($collection as $formulasModel) {
            $formulasData = $this->dataFormulasFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $formulasData,
                $formulasModel->getData(),
                \Licentia\Equity\Api\Data\FormulasInterface::class
            );
            $items[] = $this->dataObjectProcessor->buildOutputDataArray(
                $formulasData,
                \Licentia\Equity\Api\Data\FormulasInterface::class
            );
        }
        $searchResults->setItems($items);

        return $searchResults;
    }

    /**
     * {@inheritdoc}
     * @throws CouldNotDeleteException
     */
    public function delete(
        \Licentia\Equity\Api\Data\FormulasInterface $formulas
    ) {

        try {
            $this->resource->delete($formulas);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete the Formulas: %1',
                    $exception->getMessage()
                )
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($formulasId)
    {

        return $this->delete($this->getById($formulasId));
    }
}
