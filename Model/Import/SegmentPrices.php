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

class SegmentPrices extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{

    const COL_PRODUCT_ID = 'product_id';

    const COL_PRODUCT_SKU = 'sku';

    const COL_WEBSITE = 'website';

    const COL_SEGMENT_ID = 'segment_id';

    const COL_SEGMENT = 'segment';

    const COL_WEBSITE_ID = 'website_id';

    const COL_PRICE = 'price';

    const TABLE_SEGMENT_PRICES = 'panda_segments_prices';

    const VALID_FIELDS = [
        'sku',
        'segment',
        'website',
        'price',
    ];

    const AVAILABLE_IMPORT_FIELDS = [
        'product_id',
        'segment_id',
        'website_id',
        'price',
    ];

    /**
     * Validation failure message template definitions.
     *
     * @var array
     */
    protected $_messageTemplates = [
        Segments::ERROR_INVALID_SEGMENT => 'Invalid Segment ID',
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
     * Permanent entity columns.
     *
     * @var string[]
     */
    protected $_permanentAttributes = [self::COL_PRODUCT_SKU];

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

        $this->validColumnNames = self::VALID_FIELDS;
        $this->_resourceFactory = $subscribersFactory->create()->getResource();
        $this->dateTime = $dateTime;
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->_connection = $resource->getConnection('write');
        $this->_storeResolver = $storeResolver;
        $this->pricesTable = $this->_resourceFactory->getTable(self::TABLE_SEGMENT_PRICES);
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
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {

        return 'panda_segments';
    }

    /**
     * Row validation.
     *
     * @param array $rowData
     * @param int   $rowNum
     *
     * @return bool
     * @throws \Zend_Validate_Exception
     */
    public function validateRow(array $rowData, $rowNum)
    {

        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }
        $this->_validatedRows[$rowNum] = true;
        // BEHAVIOR_DELETE use specific validation logic
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            if (!isset($rowData[self::COL_PRODUCT_SKU]) ||
                !isset($rowData[self::COL_SEGMENT]) ||
                !isset($rowData[self::COL_WEBSITE])) {
                $this->addRowError(Segments::ERROR_INVALID_SKU, $rowNum);

                return false;
            }

            return true;
        }

        if (!$this->segmentsValidator->isValid($rowData)) {
            foreach ($this->segmentsValidator->getMessages() as $message) {
                $this->addRowError($message, $rowNum);
            }
        }

        $sku = false;
        if (isset($rowData[self::COL_PRODUCT_SKU])) {
            $sku = $rowData[self::COL_PRODUCT_SKU];
        }

        if (false === $sku) {
            $this->addRowError(ValidatorInterface::ERROR_ROW_IS_ORPHAN, $rowNum);
        }

        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
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
     * Deletes Subscribers data from raw data.
     *
     * @return $this
     * @throws \Exception
     */
    public function deletePrices()
    {

        $this->cachedPricesToDelete = null;
        $listPrices = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {

            foreach ($bunch as $rowNum => $rowData) {
                $this->validateRow($rowData, $rowNum);
                if (!$this->getErrorAggregator()->isRowInvalid($rowNum)) {
                    $listPrices[$rowNum] = $rowData;

                    $productId = $this->getProductId($rowData[self::COL_PRODUCT_SKU]);
                    $listPrices[$rowNum] = array_intersect_key($rowData,
                        array_flip(self::AVAILABLE_IMPORT_FIELDS));

                    $listPrices[$rowNum]['website_id'] = $this->getWebsiteId($rowData[self::COL_WEBSITE]);
                    $listPrices[$rowNum]['segment_id'] = $this->getSegmentId($rowData[self::COL_SEGMENT]);
                    $listPrices[$rowNum]['product_id'] = $productId;

                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);

                }
            }
        }

        if ($listPrices) {
            $this->deletePricesFinal($listPrices, $this->pricesTable);
        }

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
     * Save and replace advanced subscribers
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @throws \Exception
     */
    protected function saveAndReplacePrices()
    {

        $behavior = $this->getBehavior();
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $behavior) {
            $this->cachedPricesToDelete = null;
        }
        $listPrices = [];
        $prices = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {

                if (!$this->validateRow($rowData, $rowNum)) {
                    $this->addRowError('Empty Product', $rowNum);
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }

                $productId = $this->getProductId($rowData[self::COL_PRODUCT_SKU]);
                $rowPrice = $rowNum;

                $prices[$rowPrice] = array_intersect_key($rowData,
                    array_flip(self::AVAILABLE_IMPORT_FIELDS));

                if (!empty($rowData[self::COL_WEBSITE])) {
                    $prices[$rowPrice]['website_id'] = $this->getWebsiteId($rowData[self::COL_WEBSITE]);
                }
                if (!empty($rowData[self::COL_PRODUCT_SKU])) {
                    $prices[$rowPrice]['product_id'] = $productId;
                }
                if (!empty($rowData[self::COL_SEGMENT])) {
                    $prices[$rowPrice]['segment_id'] = $this->getSegmentId($rowData[self::COL_SEGMENT]);
                }

                $listPrices[$rowNum] = $rowData;

                $productId = $this->getProductId($rowData[self::COL_PRODUCT_SKU]);
                $listPrices[$rowNum] = array_intersect_key($rowData,
                    array_flip(self::AVAILABLE_IMPORT_FIELDS));

                $listPrices[$rowNum]['website_id'] = $this->getWebsiteId($rowData[self::COL_WEBSITE]);
                $listPrices[$rowNum]['segment_id'] = $this->getSegmentId($rowData[self::COL_SEGMENT]);
                $listPrices[$rowNum]['product_id'] = $productId;
            }

            if (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $behavior) {
                $this->processCountExistingPrices($prices, self::TABLE_SEGMENT_PRICES)
                     ->processCountNewPrices($prices);

                $this->savePricesExecute($prices, self::TABLE_SEGMENT_PRICES);
            }

        }

        if (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $behavior) {

            if ($listPrices) {
                $this->processCountNewPrices($prices);
                if ($this->deletePricesFinal($listPrices, self::TABLE_SEGMENT_PRICES)) {
                    $this->savePricesExecute($prices, self::TABLE_SEGMENT_PRICES);
                }
            }
        }

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
            $this->_connection->insertOnDuplicate($tableName, $prices, self::AVAILABLE_IMPORT_FIELDS);
        }

        return $this;
    }

    public function getProductId($sku)
    {

        return array_search($sku, $this->segmentsValidator->getAllSkus());

    }

    public function getSegmentId($code)
    {

        return array_search($code, $this->segmentsValidator->getSegmentsIds());

    }

    /**
     * Deletes subscribers subscribers.
     *
     * @param array  $listPrices
     * @param string $table
     *
     * @return boolean
     * @throws \Exception
     */
    protected function deletePricesFinal(array $listPrices, $table)
    {

        $tableName = $this->_resourceFactory->getTable($table);
        $PricesTablePrimaryKey = $this->getProductsTablePrimaryKey();

        if ($tableName && $listPrices) {
            if (!$this->cachedPricesToDelete) {

                $select = $this->_connection->select()
                                            ->from($this->pricesTable, [$PricesTablePrimaryKey]);

                foreach ($listPrices as $item) {
                    $select->where('product_id=?', $item['product_id']);
                    $select->where('segment_id=?', $item['segment_id']);
                    $select->where('website_id=?', $item['website_id']);
                }

                $this->cachedPricesToDelete = $this->_connection->fetchCol($select);
            }

            if ($this->cachedPricesToDelete) {
                try {
                    $this->countItemsDeleted += $this->_connection->delete(
                        $tableName,
                        $this->_connection->quoteInto($PricesTablePrimaryKey . ' IN (?)',
                            $this->cachedPricesToDelete)
                    );

                    return true;
                } catch (\Exception $e) {
                    return false;
                }
            } else {
                $this->addRowError('Product is Empty', 0);

                return false;
            }
        }

        return false;
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
                                  ->from($this->pricesTable, self::AVAILABLE_IMPORT_FIELDS)
            );
        }

        return $this->oldPrices;
    }

    /**
     * Count existing subscribers
     *
     * @param array  $newPrices
     * @param string $table
     *
     * @return $this
     * @throws \Exception
     */
    protected function processCountExistingPrices($newPrices, $table)
    {

        $oldPrices = $this->retrieveOldPrices();
        $existProductIds = array_intersect_key($oldPrices, $newPrices);

        if (!count($oldPrices)) {
            return $this;
        }

        $tableName = $this->_resourceFactory->getTable($table);
        $productEntityLinkField = $this->getProductsTablePrimaryKey();

        $select = $this->_connection->select()
                                    ->from($tableName, self::AVAILABLE_IMPORT_FIELDS);

        foreach ($oldPrices as $item) {
            $select->where('product_id=?', $item['product_id']);
            $select->where('segment_id=?', $item['segment_id']);
            $select->where('website_id=?', $item['website_id']);
        }

        $existingPrices = $this->_connection->fetchAll($select);

        foreach ($existingPrices as $existingPrice) {
            foreach ($newPrices as $prices) {
                $this->incrementCounterUpdated($prices, $existingPrice);
            }
        }

        return $this;
    }

    /**
     * @param $prices
     * @param $existingPrice
     */
    protected function incrementCounterUpdated($prices, $existingPrice)
    {

        if ($existingPrice['product_id'] == $prices['product_id']
            && $existingPrice['segment_id'] == $prices['segment_id']
            && (int) $existingPrice['website_id'] === (int) $prices['website_id']
        ) {
            $this->countItemsUpdated++;
        }
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

    /**
     * Get product entity link field
     *
     * @return string
     * @throws \Exception
     */
    private function getProductsTablePrimaryKey()
    {

        return 'price_id';
    }
}
