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
     * @var \Licentia\Equity\Model\PricesFactory
     */
    protected $pricesFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * SegmentsPrices constructor.
     *
     * @param \Magento\Framework\App\RequestInterface                       $request
     * @param StoreManagerInterface                                         $storeManager
     * @param \Licentia\Equity\Model\PricesFactory                          $pricesFactory
     * @param \Licentia\Equity\Model\ResourceModel\Prices\CollectionFactory $pricesCollection
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        StoreManagerInterface $storeManager,
        \Licentia\Equity\Model\PricesFactory $pricesFactory,
        \Licentia\Equity\Model\ResourceModel\Prices\CollectionFactory $pricesCollection
    ) {

        $this->pricesFactory = $pricesFactory;
        $this->pricesCollection = $pricesCollection;
        $this->storeManager = $storeManager;
        $this->request = $request;
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

        $new = $this->request->getParam('product');
        $priceRows = $new['panda_segments'] ?? [];

        // prepare data for save
        $new = [];
        foreach ($priceRows as $data) {
            $new[] = [
                'segment_id' => $data['segment_id'],
                'product_id' => $object->getId(),
                'website_id' => $data['website_id'],
                'price'      => $data['price'],
            ];
        }

        $collection = $this->pricesCollection->create()->addFieldToFilter('product_id', $object->getId());
        $collection->walk('delete');

        foreach ($new as $item) {
            $this->pricesFactory->create()
                                ->setData($item)
                                ->save();

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

        return __('Duplicated records found in your segments prices. Products can only have one price per segment');
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
