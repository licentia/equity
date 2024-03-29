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

namespace Licentia\Equity\Model\Import;

use Magento\CatalogImportExport\Model\Import\Product\RowValidatorInterface as ValidatorInterface;
use \Licentia\Equity\Model\Import\Validator\Segments;

class SegmentProducts extends ImportAbstract
{

    const COL_PRODUCT_ID = 'product_id';

    const COL_PRODUCT_SKU = 'sku';

    const COL_SEGMENT_ID = 'segment_id';

    const COL_SEGMENT = 'segment';

    const PANDA_TABLE_NAME = 'panda_segments_products';

    const VALID_FIELDS = [
        'sku',
        'segment',
    ];

    const AVAILABLE_IMPORT_FIELDS = [
        'product_id',
        'segment_id',
    ];

    /**
     * Validation failure message template definitions.
     *
     * @var array
     */
    protected $_messageTemplates = [
        Segments::ERROR_INVALID_SEGMENT => 'Invalid Segment ID',
        Segments::ERROR_INVALID_SKU     => 'Invalid SKU',
    ];

    /**
     * Permanent entity columns.
     *
     * @var string[]
     */
    protected $_permanentAttributes = [self::COL_PRODUCT_SKU, self::COL_SEGMENT];

    /**z
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {

        return 'panda_products';
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

                    $productId = $this->segmentsValidator->getProductId($rowData[self::COL_PRODUCT_SKU]);
                    $listPrices[$rowNum] = array_intersect_key($rowData,
                        array_flip(self::AVAILABLE_IMPORT_FIELDS));

                    $listPrices[$rowNum]['segment_id'] = $this->segmentsValidator->getSegmentId($rowData[self::COL_SEGMENT]);
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

                $productId = $this->segmentsValidator->getProductId($rowData[self::COL_PRODUCT_SKU]);
                $rowPrice = $rowNum;

                $prices[$rowPrice] = array_intersect_key($rowData, array_flip(self::AVAILABLE_IMPORT_FIELDS));

                $prices[$rowPrice]['product_id'] = $productId;
                $prices[$rowPrice]['segment_id'] = $this->segmentsValidator->getSegmentId($rowData[self::COL_SEGMENT]);

            }

            if (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $behavior) {
                $this->processCountExistingPrices($prices, self::PANDA_TABLE_NAME)
                     ->processCountNewPrices($prices);

                $this->savePricesExecute($prices, self::PANDA_TABLE_NAME);
            }

        }

        if (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $behavior) {

            if ($prices) {
                $this->processCountNewPrices($prices);
                if ($this->deletePricesFinal($prices, self::PANDA_TABLE_NAME)) {
                    $this->savePricesExecute($prices, self::PANDA_TABLE_NAME);
                }
            }
        }

        return $this;
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
        $connection = $this->_connection;

        if ($tableName && $listPrices) {
            if (!$this->cachedPricesToDelete) {

                $select = $this->_connection->select()
                                            ->from($this->pricesTable, [$PricesTablePrimaryKey]);

                foreach ($listPrices as $item) {
                    $sql = '';
                    $sql .= $connection->quoteInto(' product_id=? ', $item['product_id']);
                    $sql .= $connection->quoteInto(' AND segment_id=? ', $item['segment_id']);

                    $select->orWhere($sql);
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
            }
        }

        return false;
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

        if (!count($oldPrices)) {
            return $this;
        }

        $tableName = $this->_resourceFactory->getTable($table);

        $select = $this->_connection->select()
                                    ->from($tableName, self::AVAILABLE_IMPORT_FIELDS);

        foreach ($oldPrices as $item) {
            $select->where('product_id=?', $item['product_id']);
            $select->where('segment_id=?', $item['segment_id']);
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
        ) {
            $this->countItemsUpdated++;
        }
    }

    /**
     * Get product entity link field
     *
     * @return string
     * @throws \Exception
     */
    private function getProductsTablePrimaryKey()
    {

        return 'record_id';
    }

    /**
     *
     */
    public function updateTotals()
    {

        $select = $this->_connection->fetchPairs(
            $this->_connection->select()
                              ->from(self::PANDA_TABLE_NAME, ['segment_id', 'COUNT(*)'])
                              ->group('segment_id')
        );

        $this->_connection->update($this->_resourceFactory->getTable('panda_segments'),
            [
                'number_products' => 0,
            ]);

        foreach ($select as $segmentId => $total) {
            $this->_connection->update($this->_resourceFactory->getTable('panda_segments'),
                [
                    'number_products' => $total,
                ],
                [
                    'segment_id=?' => $segmentId,
                ]);
        }
    }

}
