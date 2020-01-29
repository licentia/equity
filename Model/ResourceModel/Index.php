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

namespace Licentia\Equity\Model\ResourceModel;

/**
 * Class Index
 *
 * @package Licentia\Equity\Model\ResourceModel
 */
class Index extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Prices\CollectionFactory
     */
    protected $pricesCollection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Index constructor.
     *
     * @param \Magento\Catalog\Api\ProductRepositoryInterface    $productRepository
     * @param Prices\CollectionFactory                           $pricesCollection
     * @param \Magento\Framework\Model\ResourceModel\Db\Context  $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManagerInterface
     * @param null                                               $resourcePrefix
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Licentia\Equity\Model\ResourceModel\Prices\CollectionFactory $pricesCollection,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        $resourcePrefix = null
    ) {

        parent::__construct($context, $resourcePrefix);

        $this->productRepository = $productRepository;
        $this->pricesCollection = $pricesCollection;
        $this->storeManager = $storeManagerInterface;
        $this->scopeConfig = $scopeInterface;
    }

    /**
     * Initialize resource model
     * Get tablename from config
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init('panda_segments_prices_idx', 'index_id');
    }

    /**
     * @param $product
     *
     * @return $this|bool
     */
    public function reindexProduct($product)
    {

        if (!$this->scopeConfig->isSetFlag('panda_magna/prices/enabled')) {
            return false;
        }

        try {
            $product = $this->productRepository->get($product);
        } catch (\Exception $e) {
            return false;
        }

        $productId = $product->getId();
        $adapter = $this->getConnection();
        $websiteIds = array_keys($this->storeManager->getWebsites());

        $where = [
            $adapter->quoteInto('product_id = ?', $productId),
        ];

        $adapter->delete($this->getMainTable(), $where);

        $collection = $this->pricesCollection->create()->addFieldToFilter('product_id', $productId);

        foreach ($collection as $item) {
            if ($item->getWebsiteId() == 0) {
                foreach ($websiteIds as $websiteId) {
                    $item->setData('website_id', $websiteId);
                    $data = $this->_prepareDataForSave($item);
                    $adapter->insert($this->getMainTable(), $data);
                }
            } else {
                $data = $this->_prepareDataForSave($item);
                $adapter->insert($this->getMainTable(), $data);
            }
        }

        return $this;
    }
}
