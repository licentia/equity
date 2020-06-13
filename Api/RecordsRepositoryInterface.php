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
 * Interface RecordsRepositoryInterface
 *
 * @package Licentia\Panda\Api
 */
interface RecordsRepositoryInterface
{

    /**
     * Save Records
     *
     * @param Data\RecordsInterface $records
     *
     * @return \Licentia\Equity\Api\Data\RecordsInterface
     */

    public function save(
        \Licentia\Equity\Api\Data\RecordsInterface $records
    );

    /**
     * Retrieve Records
     *
     * @param string $recordsId
     *
     * @return \Licentia\Equity\Api\Data\RecordsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getById($recordsId);

    /**
     * Retrieve Records matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Licentia\Equity\Api\Data\RecordsSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Records
     *
     * @param Data\RecordsInterface $records
     *
     * @return bool true on success
     */

    public function delete(
        \Licentia\Equity\Api\Data\RecordsInterface $records
    );

    /**
     * Delete Records by ID
     *
     * @param string $recordsId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function deleteById($recordsId);
}
