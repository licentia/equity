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
    protected \Licentia\Equity\Model\ResourceModel\SegmentsFactory $resource;

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
