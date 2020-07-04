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

/**
 * Class Access
 *
 * @package Licentia\Panda\Model
 */
class CustomerPrices extends \Magento\Framework\Model\AbstractModel
    implements \Licentia\Equity\Api\PricesRepositoryInterface
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_customer_prices';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'panda_customer_prices';

    /**
     * @var Import\Validator\Segments
     */
    protected $validator;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\CustomerPrices::class);
    }

    public function __construct(
        \Licentia\Equity\Model\Import\Validator\Segments $validator,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->validator = $validator;
    }

    /**
     * @param string $prices
     *
     * @return int
     * @throws \Exception
     */
    public function import($prices)
    {

        $prices = json_decode($prices, true);
        if (!is_array($prices)) {
            throw new \Exception(__('Input must be a valid json string convertible into array'));
        }

        $validFields = Import\CustomerPrices::VALID_FIELDS;
        $saveFields = Import\CustomerPrices::AVAILABLE_IMPORT_FIELDS;
        $valid = [];
        foreach ($prices as $index => $price) {

            if (!is_array($price)) {
                throw new \Exception(__('Input must be a valid json string convertible into array'));
            }

            if (array_diff_key($price, array_flip($validFields))) {
                throw new \Exception(__('Required indexes: ' . implode(',', $validFields)));
            }

            if ($this->validator->isValid($price)) {
                $valid[$index] = $price;

                $productId = $this->validator->getProductId($price['sku']);
                $valid[$index]['website_id'] = $this->validator->getWebsiteId($price['website']);
                $valid[$index]['product_id'] = $productId;
                $valid[$index]['price'] = $price['price'];
                $valid[$index]['customer_id'] = $this->validator->getCustomerId($price['email'], $price);

                $valid[$index] = array_intersect_key($valid[$index], array_flip($saveFields));

            } else {
                foreach ($this->validator->getMessages() as $message) {
                    throw new \Exception(__($message));
                }
            }

        }

        if ($valid) {
            $tableName = $this->getResource()->getMainTable();

            return $this->getResource()->getConnection()->insertOnDuplicate($tableName, $valid, $saveFields);
        }

        return 0;

    }

    /**
     * @param string $prices
     *
     * @return int
     * @throws \Exception
     */
    public function remove($prices)
    {

        $prices = json_decode($prices, true);
        if (!is_array($prices)) {
            throw new \Exception(__('Input must be a valid json string convertible into array'));
        }

        $validFields = Import\CustomerPrices::VALID_FIELDS;
        unset($validFields['price']);
        $saveFields = Import\CustomerPrices::AVAILABLE_IMPORT_FIELDS;
        $valid = [];

        foreach ($prices as $index => $price) {

            if (!is_array($price)) {
                throw new \Exception(__('Input must be a valid json string convertible into array'));
            }

            if (array_diff_key($price, array_flip($validFields))) {
                throw new \Exception(__('Required indexes: ' . implode(',', $validFields)));
            }

            $valid[$index] = $price;
            $productId = $this->validator->getProductId($price['sku']);
            $valid[$index]['website_id'] = $this->validator->getWebsiteId($price['website']);
            $valid[$index]['product_id'] = $productId;
            $valid[$index]['customer_id'] = $this->validator->getCustomerId($price['email'], $price);

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
                $sql .= $connection->quoteInto(' AND website_id=? ', $item['website_id']);
                $sql .= $connection->quoteInto(' AND customer_id=? ', $item['customer_id']);

                $select->orWhere($sql);
            }

            $toDelete = $connection->fetchCol($select);

            return $connection->delete(
                $resource->getMainTable(),
                $connection->quoteInto($this->getIdFieldName() . ' IN (?)', $toDelete)
            );

        }

        return 0;

    }
}
