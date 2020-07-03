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

namespace Licentia\Equity\Model\Import;

use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use Magento\CatalogImportExport\Model\Import\Product\RowValidatorInterface as ValidatorInterface;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use \Licentia\Equity\Model\Import\Validator\Segments;

abstract class ImportAbstract extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{

    /**
     * Validation failure message template definitions.
     *
     * @var array
     */
    protected $_messageTemplates = [
        Segments::ERROR_INVALID_SKU     => 'Invalid SKU',
        Segments::ERROR_INVALID_WEBSITE => 'Invalid Website code',
        Segments::ERROR_INVALID_PRICE   => 'Invalid Price Format',
    ];

    /**
     * If we should check column names
     *
     * @var bool
     */
    protected $needColumnCheck = true;

    /**
     * Need to log in import history
     *
     * @var bool
     */
    protected $logInHistory = true;

    /**
     * @var \Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModelFactory
     */
    protected $_resourceFactory;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogData;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_productModel;

    /**
     * @var \Magento\CatalogImportExport\Model\Import\Product\StoreResolver
     */
    protected $_storeResolver;

    /**
     * @var ImportProduct
     */
    protected $_importProduct;

    /**
     * @var array
     */
    protected $_validators = [];

    /**
     * @var array
     */
    protected $cachedPricesToDelete;

    /**
     * @var array
     */
    protected $oldPrices = null;

    /**
     * Catalog product entity
     *
     * @var string
     */
    protected $pricesTable;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var Validator\Segments
     */
    protected $segmentsValidator;

    protected $customers;

    /**
     * Product entity link field
     *
     * @var string
     */
    private $subscribersTablePrimaryKey;

    /**
     * SegmentPrices constructor.
     *
     * @param \Magento\Framework\Json\Helper\Data                   $jsonHelper
     * @param \Magento\ImportExport\Helper\Data                     $importExportData
     * @param \Magento\ImportExport\Model\ResourceModel\Import\Data $importData
     * @param \Magento\Framework\App\ResourceConnection             $resource
     * @param \Magento\ImportExport\Model\ResourceModel\Helper      $resourceHelper
     * @param \Magento\Framework\Stdlib\StringUtils                 $string
     * @param ProcessingErrorAggregatorInterface                    $errorAggregator
     * @param \Magento\Framework\Stdlib\DateTime\DateTime           $dateTime
     * @param ImportProduct\StoreResolver                           $storeResolver
     * @param \Licentia\Panda\Model\SubscribersFactory              $subscribersFactory
     * @param Segments                                              $segmentsValidator
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\CatalogImportExport\Model\Import\Product\StoreResolver $storeResolver,
        \Licentia\Panda\Model\SubscribersFactory $subscribersFactory,
        Segments $segmentsValidator
    ) {

        $this->validColumnNames = static::VALID_FIELDS;
        $this->_resourceFactory = $subscribersFactory->create()->getResource();
        $this->dateTime = $dateTime;
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->_connection = $resource->getConnection('write');
        $this->_storeResolver = $storeResolver;
        $this->pricesTable = $this->_resourceFactory->getTable(static::PANDA_TABLE_NAME);
        $this->oldPrices = $this->retrieveOldPrices();
        $this->errorAggregator = $errorAggregator;
        $this->segmentsValidator = $segmentsValidator;

        $this->_messageTemplates[Segments::ERROR_INVALID_WEBSITE] = 'Invalid Website Code. Available: (' . implode(',',
                $segmentsValidator->getWebsitesIds()) . ')';

        foreach (array_merge($this->errorMessageTemplates, $this->_messageTemplates) as $errorCode => $message) {
            $this->getErrorAggregator()->addErrorMessageTemplate($errorCode, $message);
        }

    }

    /**
     * Create Subscribers data from raw data.
     *
     * @return bool Result of operation.
     * @throws \Exception
     */
    protected function _importData()
    {

        if (\Magento\ImportExport\Model\Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            $this->deletePrices();
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $this->getBehavior()) {
            $this->replacePrices();
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $this->getBehavior()) {
            $this->savePrices();
        }

        return true;
    }

    /**
     * Save subscribers
     *
     * @return $this
     * @throws \Exception
     */
    public function savePrices()
    {

        $this->saveAndReplacePrices();

        return $this;
    }

    /**
     * Replace subscribers
     *
     * @return $this
     * @throws \Exception
     */
    public function replacePrices()
    {

        $this->saveAndReplacePrices();

        return $this;
    }

    /**
     * Save product subscribers.
     *
     * @param array  $prices
     * @param string $table
     *
     * @return $this
     * @throws \Exception
     */
    protected function savePricesExecute(array $prices, $table)
    {

        if ($prices) {
            $tableName = $this->_resourceFactory->getTable($table);
            $this->_connection->insertOnDuplicate($tableName, $prices, static::AVAILABLE_IMPORT_FIELDS);
        }

        return $this;
    }

    public function getProductId($sku)
    {

        return array_search($sku, $this->segmentsValidator->getAllSkus());

    }

    public function getSegmentId($code)
    {

        return array_search($code, $this->segmentsValidator->getManualSegmentsIds());

    }

    /**
     * Get store id by code
     *
     * @param string $websiteId
     *
     * @return array|int|string
     */
    protected function getWebsiteId($websiteId)
    {

        return $this->_storeResolver->getWebsiteCodeToId($websiteId);
    }

    /**
     * Retrieve product skus
     *
     * @return array
     * @throws \Exception
     */
    protected function retrieveOldPrices()
    {

        if ($this->oldPrices === null) {
            $this->oldPrices = $this->_connection->fetchAll(
                $this->_connection->select()
                                  ->from($this->pricesTable, static::AVAILABLE_IMPORT_FIELDS)
            );
        }

        return $this->oldPrices;
    }

    /**
     * Count new subscribers
     *
     * @param array $prices
     *
     * @return $this
     */
    protected function processCountNewPrices(array $prices)
    {

        $this->countItemsCreated = count($prices);

        $this->countItemsCreated -= $this->countItemsUpdated;

        return $this;
    }

}
