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
}
