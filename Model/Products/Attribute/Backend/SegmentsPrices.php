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

namespace Licentia\Equity\Model\Products\Attribute\Backend;

use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Segments
 *
 * @package Licentia\Equity\Model\Products\Attribute\Backend
 */
class SegmentsPrices extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Licentia\Equity\Model\ResourceModel\Prices\CollectionFactory
     */
    protected $pricesCollection;

    /**
     * @var \Licentia\Equity\Model\ResourceModel\Index\CollectionFactory
     */
    protected $indexCollection;

    /**
     * @var \Licentia\Equity\Model\PricesFactory
     */
    protected $pricesFactory;

    /**
     * @var \Licentia\Equity\Model\IndexFactory
     */
    protected $indexFactory;

    /**
     * SegmentsPrices constructor.
     *
     * @param StoreManagerInterface                                         $storeManager
     * @param \Licentia\Equity\Model\PricesFactory                          $pricesFactory
     * @param \Licentia\Equity\Model\IndexFactory                           $indexFactory
     * @param \Licentia\Equity\Model\ResourceModel\Index\CollectionFactory  $indexCollection
     * @param \Licentia\Equity\Model\ResourceModel\Prices\CollectionFactory $pricesCollection
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        \Licentia\Equity\Model\PricesFactory $pricesFactory,
        \Licentia\Equity\Model\IndexFactory $indexFactory,
        \Licentia\Equity\Model\ResourceModel\Index\CollectionFactory $indexCollection,
        \Licentia\Equity\Model\ResourceModel\Prices\CollectionFactory $pricesCollection
    ) {

        $this->indexFactory = $indexFactory;
        $this->pricesFactory = $pricesFactory;
        $this->pricesCollection = $pricesCollection;
        $this->indexCollection = $indexCollection;
        $this->storeManager = $storeManager;
    }

    /**
     * Assign group prices to product data
     *
     * @param \Magento\Catalog\Model\Product $object
     *
     * @return $this
     */
    public function afterLoad($object)
    {

        /** @var \Licentia\Equity\Model\ResourceModel\Prices\Collection $collection */
        $collection = $this->pricesCollection->create()->addFieldToFilter('product_id', $object->getId());

        $data = $collection->getData();

        $object->setData(
            $this->getAttribute()
                 ->getName(),
            $data
        );
        $object->setOrigData(
            $this->getAttribute()
                 ->getName(),
            $data
        );

        return $this;
    }

    /**
     * After Save Attribute manipulation
     *
     * @param \Magento\Catalog\Model\Product $object
     *
     * @return $this
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function afterSave($object)
    {

        $priceRows = $object->getData(
            $this->getAttribute()
                 ->getName()
        );
        if (null === $priceRows) {
            return $this;
        }

        // prepare data for save
        $new = [];
        foreach ($priceRows as $data) {
            if (isset($data['delete']) && $data['delete'] == 1) {
                continue;
            }
            $new[] = [
                'segment_id' => $data['segment_id'],
                'product_id' => $object->getId(),
                'website_id' => $data['website_id'],
                'price'      => $data['price'],
            ];
        }

        $collection = $this->pricesCollection->create()->addFieldToFilter('product_id', $object->getId());
        $collection->walk('delete');

        $index = $this->indexCollection->create()->addFieldToFilter('product_id', $object->getId());
        $index->walk('delete');

        foreach ($new as $item) {
            $this->pricesFactory->create()
                                ->setData($item)
                                ->save();

            if ($item['website_id'] == 0) {
                foreach ($this->getWebsites() as $websiteId) {
                    $item['website_id'] = $websiteId;
                    $this->indexFactory->create()
                                       ->setData($item)
                                       ->save();
                }
            } else {
                $this->indexFactory->create()
                                   ->setData($item)
                                   ->save();
            }
        }

        return $this;
    }

    /**
     * @param \Magento\Framework\DataObject $object
     *
     * @return bool|\Magento\Framework\Phrase
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate($object)
    {

        $attribute = $this->getAttribute();
        $priceRows = $object->getData($attribute->getName());
        $priceRows = array_filter((array) $priceRows);

        if (empty($priceRows)) {
            return true;
        }

        // validate per website
        $duplicates = [];
        foreach ($priceRows as $priceRow) {
            if (!empty($priceRow['delete'])) {
                continue;
            }
            $compare = implode('-', [$priceRow['segment_id'], $priceRow['website_id']]);
            if (isset($duplicates[$compare])) {
                throw new \Magento\Framework\Exception\LocalizedException(__($this->_getDuplicateErrorMessage()));
            }

            if (isset($priceRow['price']) && $priceRow['price'] < 1) {
                return __('Price should be greater than 0');
            }

            $duplicates[$compare] = true;
        }

        return true;
    }

    /**
     * @return string
     */
    protected function _getDuplicateErrorMessage()
    {

        return __('Duplicated records found');
    }

    /**
     * @return array
     */
    protected function getWebsites()
    {

        $websites = [];
        $websitesList = $this->storeManager->getWebsites();

        foreach ($websitesList as $website) {
            $websites[$website->getId()] = $website->getId();
        }

        return $websites;
    }
}
