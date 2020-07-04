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

namespace Licentia\Equity\Api;

/**
 * Interface SegmentsRepositoryInterface
 *
 * @package Licentia\Panda\Api
 */
interface SegmentsRepositoryInterface
{

    /**
     * Save Segments
     *
     * @param Data\SegmentsInterface $segments
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */

    public function save(
        \Licentia\Equity\Api\Data\SegmentsInterface $segments
    );

    /**
     * Retrieve Segments
     *
     * @param string $segmentsId
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getById($segmentsId);

    /**
     * Retrieve Segments matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Licentia\Equity\Api\Data\SegmentsSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Segments
     *
     * @param Data\SegmentsInterface $segments
     *
     * @return bool true on success
     */

    public function delete(
        \Licentia\Equity\Api\Data\SegmentsInterface $segments
    );

    /**
     * Delete Segments by ID
     *
     * @param string $segmentsId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function deleteById($segmentsId);

    /**
     * save Products
     *
     * @param string $products
     *
     * @return int
     */

    public function saveProducts($products);

    /**
     * Remove Products
     *
     * @param string $products
     *
     * @return int
     */

    public function removeProducts($products);

}
