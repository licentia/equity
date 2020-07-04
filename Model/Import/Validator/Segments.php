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

namespace Licentia\Equity\Model\Import\Validator;

use Magento\AdvancedPricingImportExport\Model\Import\AdvancedPricing;
use Magento\CatalogImportExport\Model\Import\Product\Validator\AbstractImportValidator;
use Magento\CatalogImportExport\Model\Import\Product\RowValidatorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

class Segments extends AbstractImportValidator implements RowValidatorInterface
{

    const ERROR_INVALID_CUSTOMER_ID = 'invalidCustID';

    const ERROR_INVALID_SKU = 'invalidSku';

    const ERROR_INVALID_SEGMENT = 'invalidSegment';

    const ERROR_INVALID_PRICE = 'invalidPrice';

    const ERROR_INVALID_CUSTOMER_EMAIL = 'invalidCustEmail';

    /**
     * @var array
     */
    protected $skus;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var array
     */
    protected $segments;

    /**
     * @var array
     */
    protected $websites;

    protected $customers;

    protected $segmentsManual;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @var \Magento\CatalogImportExport\Model\Import\Product\StoreResolver
     */
    protected $_storeResolver;

    public $caller = null;

    /**
     * Segments constructor.
     *
     * @param \Licentia\Panda\Helper\Data $pandaHelper
     * @param ResourceConnection          $resourceConnection
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        ResourceConnection $resourceConnection,
        \Magento\CatalogImportExport\Model\Import\Product\StoreResolver $storeResolver
    ) {

        $this->resourceConnection = $resourceConnection;
        $this->pandaHelper = $pandaHelper;
        $this->connection = $this->resourceConnection->getConnection();
        $this->_storeResolver = $storeResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function init($context)
    {

        return parent::init($context);
    }

    /**
     * Validate value
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isValid($value)
    {

        $this->_clearMessages();
        $valid = true;

        if (isset($value['sku']) &&
            !in_array($value['sku'], $this->getAllSkus())) {
            $this->_addMessages([self::ERROR_INVALID_SKU]);
            $valid = false;
        }

        if (isset($value['price']) &&
            !is_numeric($value['price'])) {
            $this->_addMessages([self::ERROR_INVALID_PRICE]);
            $valid = false;
        }

        if ($this->caller == 'panda_products') {
            if (isset($value['segment']) &&
                !in_array($value['segment'], $this->getCatalogSegmentsIds())) {
                $this->_addMessages([self::ERROR_INVALID_SEGMENT]);
                $valid = false;
            }
        } else {
            if (isset($value['segment']) &&
                !in_array($value['segment'], $this->getSegmentsIds())) {
                $this->_addMessages([self::ERROR_INVALID_SEGMENT]);
                $valid = false;
            }
        }

        if (isset($value['website']) &&
            !in_array($value['website'], $this->getWebsitesIds())) {
            $this->_addMessages([self::ERROR_INVALID_WEBSITE]);
            $valid = false;
        }

        if (isset($value['email']) &&
            !$this->getCustomerId($value['email'], $value)) {
            $this->_addMessages([self::ERROR_INVALID_CUSTOMER_EMAIL]);
            $valid = false;
        }

        return $valid;
    }

    /**
     * @return array
     */
    public function getAllSkus()
    {

        if (!$this->skus) {

            $this->skus = $this->connection->fetchPairs(
                $this->connection->select()
                                 ->from(
                                     $this->resourceConnection->getTableName('catalog_product_entity'),
                                     ['entity_id', 'sku']
                                 )
            );

        }

        return $this->skus;
    }

    /**
     * @return array
     */
    public function getSegmentsIds()
    {

        if (!$this->segments) {

            $this->segments = $this->connection->fetchPairs(
                $this->connection->select()
                                 ->from(
                                     $this->resourceConnection->getTableName('panda_segments'),
                                     ['segment_id', 'code']
                                 )
            );
        }

        return $this->segments;
    }

    /**
     * @return array
     */
    public function getCatalogSegmentsIds()
    {

        if (!$this->segmentsManual) {

            $this->segmentsManual = $this->connection->fetchPairs(
                $this->connection->select()
                                 ->from(
                                     $this->resourceConnection->getTableName('panda_segments'),
                                     ['segment_id', 'code']
                                 )
                                 ->where('use_as_catalog=?', 1)
            );
        }

        return $this->segmentsManual;
    }

    /**
     * @return array
     */
    public function getWebsitesIds()
    {

        if (!$this->websites) {

            $this->websites = $this->connection->fetchPairs(
                $this->connection->select()
                                 ->from(
                                     $this->resourceConnection->getTableName('store_website'),
                                     ['website_id', 'code']
                                 )
                                 ->where('code!=?', 'admin')
            );
        }

        return $this->websites;
    }

    /**
     * @return array
     */
    public function getAllCustomers()
    {

        if (!$this->customers) {

            $this->customers = $this->connection->fetchAll(
                $this->connection->select()
                                 ->from(
                                     $this->resourceConnection->getTableName('customer_entity'),
                                     ['entity_id', 'email', 'website_id']
                                 )
            );
        }

        return $this->customers;
    }

    /**
     * @param $email
     * @param $websiteId
     *
     * @return mixed|null
     */
    public function getCustomerId($email, $row)
    {

        if (!isset($row['website_id'])) {
            $websiteId = $this->_storeResolver->getWebsiteCodeToId($row['website']);
        } else {
            $websiteId = $row['website_id'];
        }

        $customers = $this->getAllCustomers();

        foreach ($customers as $customer) {
            if ($customer['email'] == $email && $customer['website_id'] == $websiteId) {
                return $customer['entity_id'];
            }
        }

        return null;
    }

}
