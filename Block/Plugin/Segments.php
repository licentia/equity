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

namespace Licentia\Equity\Block\Plugin;

/**
 * Class Segments
 *
 * @package Licentia\Panda\Block\Plugin
 */
class Segments
{

    /**
     * @var \Licentia\Equity\Model\ResourceModel\SegmentsFactory
     */
    protected $resource;

    /**
     * Segments constructor.
     *
     * @param \Licentia\Equity\Model\ResourceModel\SegmentsFactory $resource
     */
    public function __construct(
        \Licentia\Equity\Model\ResourceModel\SegmentsFactory $resource
    ) {

        $this->resource = $resource;
    }

    /**
     * @param $customerId
     *
     * @return array
     */
    public function getCustomerSegmentsIds($customerId)
    {

        $resource = $this->resource->create();
        $query = $resource->getConnection()
                          ->select()
                          ->distinct()
                          ->from($resource->getTable('panda_segments_records'), ['segment_id'])
                          ->joinLeft(
                              ['s' => $resource->getTable('panda_segments')],
                              's.segment_id = ' . $resource->getTable('panda_segments_records') . '.segment_id',
                              [])
                          ->where('s.use_in_acl =? OR s.use_in_pricing=?', 1)
                          ->where('customer_id=?', $customerId);

        return $resource->getConnection()
                        ->fetchCol($query);
    }
}
