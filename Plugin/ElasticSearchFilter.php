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

namespace Licentia\Equity\Plugin;

/**
 * Class ElasticSearchFilter
 *
 * @package Licentia\Equity\Plugin
 */
class ElasticSearchFilter
{

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected $pandaHelper;

    /**
     * ElasticSearchFilter constructor.
     *
     * @param \Licentia\Equity\Helper\Data $helper
     */
    public function __construct(
        \Licentia\Equity\Helper\Data $helper
    ) {

        $this->pandaHelper = $helper;
    }

    /**
     * @param $subject
     * @param $query
     *
     * @return array
     */
    public function beforeQuery($subject, $query)
    {

        $customerSegments = $this->pandaHelper->getCustomerSegmentsIds();
        $connection = $this->pandaHelper->getConnection();
        $resource = $this->pandaHelper->getResource();

        $allIds = [];
        if (!$customerSegments) {
            $allIds = $connection->fetchCol(
                $connection
                    ->select()
                    ->from($resource->getTable('panda_segments_products'),
                        ['product_id'])
            );
        }

        if ($customerSegments) {
            $allIds = $connection->fetchCol(
                $connection
                    ->select()
                    ->from($resource->getTable('panda_segments_products'),
                        ['product_id'])
                    ->where('segment_id NOT IN (?)', $customerSegments)
            );
        }

        if ($allIds) {
            $allIds = array_unique($allIds);
            $query['body']['query']['bool']['must_not']['terms']['_id'] = $allIds;
        }

        #unset($query['body']['query']['bool']['should']);
        #unset($query['body']['query']['bool']['minimum_should_match']);
        #$query['body']['query']['bool']['must'][]['term']['color'] = 53;

        return [$query];
    }
}
