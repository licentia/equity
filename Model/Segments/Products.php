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

namespace Licentia\Equity\Model\Segments;

/**
 * Class Records
 *
 * @package Licentia\Panda\Model
 */
class Products extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @var \Licentia\Equity\Model\Import\Validator\Segments
     */
    protected $validator;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    public function __construct(
        \Licentia\Equity\Model\Import\Validator\Segments $validator,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->pandaHelper = $pandaHelper;
        $this->validator = $validator;
    }

    /**
     * @return void
     */
    protected function _construct()
    {

        $this->_init(\Licentia\Equity\Model\ResourceModel\Segments\Products::class);
    }

    /**
     * @param \Magento\Framework\Data\Collection\AbstractDb $collection
     *
     * @return \Magento\Framework\Data\Collection\AbstractDb
     */
    public function addToCollection(\Magento\Framework\Data\Collection\AbstractDb $collection)
    {

        $customerSegments = $this->pandaHelper->getCustomerSegmentsIds();
        $connection = $collection->getResource()->getConnection();

        $parts = $collection->getSelect()->getPart('from');

        if (isset($parts['e']['tableName']) &&
            isset($parts['e']['tableName']) == $collection->getResource()->getTable('catalog_product_entity')) {

            $allCatalogs = $connection->fetchCol(
                $connection->select()
                           ->from($collection->getResource()->getTable('panda_segments'), ['segment_id'])
                           ->where('manual=?', 1)
            );

            $collection->getSelect()
                       ->joinLeft(
                           ['p' => $collection->getResource()->getTable('panda_segments_products')],
                           'e.entity_id = p.product_id'
                           , ['segment_id']
                       );

            if ($customerSegments) {
                $collection->getSelect()->where('p.segment_id IS NULL OR p.segment_id IN (?)', $customerSegments);
            } else {
                $collection->getSelect()->where('p.segment_id IS NULL OR p.segment_id NOT IN (?)', $allCatalogs);
            }

        }

        return $collection;
    }

    /**
     * @param string $products
     *
     * @return array
     */
    public function saveProducts($products)
    {

        $products = json_decode($products, true);
        if (!is_array($products)) {
            throw new \Exception(__('Input must be a valid json string convertible into array'));
        }

        $validFields = \Licentia\Equity\Model\Import\SegmentProducts::VALID_FIELDS;
        $saveFields = \Licentia\Equity\Model\Import\SegmentProducts::AVAILABLE_IMPORT_FIELDS;
        $valid = [];
        foreach ($products as $index => $price) {

            if (!is_array($price)) {
                throw new \Exception(__('Input must be a valid json string convertible into array'));
            }

            if (array_diff_key($price, array_flip($validFields))) {
                throw new \Exception(__('Required indexes: ' . implode(',', $validFields)));
            }

            if ($this->validator->isValid($price)) {
                $valid[$index] = $price;

                $productId = $this->validator->getProductId($price['sku']);
                $valid[$index]['product_id'] = $productId;
                $valid[$index]['segment_id'] = $this->validator->getSegmentId($price['segment']);

                $valid[$index] = array_intersect_key($valid[$index], array_flip($saveFields));

            } else {
                foreach ($this->validator->getMessages() as $message) {
                    throw new \Exception(__($message));
                }
            }

        }
        if ($valid) {
            $tableName = $this->getResource()->getMainTable();

            $results = $this->getResource()->getConnection()->insertOnDuplicate($tableName, $valid, $saveFields);

            return [['affected' => $results, 'success' => true]];

        }

        return [['affected' => 0, 'success' => false]];

    }

    /**
     * @param string $products
     *
     * @return array
     * @throws \Exception
     */
    public function removeProducts($products)
    {

        $products = json_decode($products, true);
        if (!is_array($products)) {
            throw new \Exception(__('Input must be a valid json string convertible into array'));
        }

        $validFields = \Licentia\Equity\Model\Import\SegmentProducts::VALID_FIELDS;
        $saveFields = \Licentia\Equity\Model\Import\SegmentProducts::AVAILABLE_IMPORT_FIELDS;
        $valid = [];

        foreach ($products as $index => $price) {

            if (!is_array($price)) {
                throw new \Exception(__('Input must be a valid json string convertible into array'));
            }

            if (array_diff_key($price, array_flip($validFields))) {
                throw new \Exception(__('Required indexes: ' . implode(',', $validFields)));
            }

            $valid[$index] = $price;
            $productId = $this->validator->getProductId($price['sku']);
            $valid[$index]['product_id'] = $productId;
            $valid[$index]['segment_id'] = $this->validator->getSegmentId($price['segment']);

            $valid[$index] = array_intersect_key($valid[$index], array_flip($saveFields));

        }

        if ($valid) {

            $resource = $this->getResource();
            $connection = $resource->getConnection();

            $select = $connection->select()
                                 ->from($resource->getMainTable(), [$this->getIdFieldName()]);

            foreach ($valid as $item) {
                $sql = '';
                $sql .= $connection->quoteInto(' product_id=? ', $item['product_id']);
                $sql .= $connection->quoteInto(' AND segment_id=? ', $item['segment_id']);

                $select->orWhere($sql);
            }

            $toDelete = $connection->fetchCol($select);

            $results = $connection->delete(
                $resource->getMainTable(),
                $connection->quoteInto($this->getIdFieldName() . ' IN (?)', $toDelete)
            );

            return [['affected' => $results, 'success' => true]];

        }

        return [['affected' => 0, 'success' => false]];

    }
}
