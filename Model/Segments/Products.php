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

namespace Licentia\Equity\Model\Segments;

/**
 * Class Records
 *
 * @package Licentia\Panda\Model
 */
class Products extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->pandaHelper = $pandaHelper;
    }

    /**
     * @return void
     */
    protected function _construct()
    {

        $this->_init(\Licentia\Equity\Model\ResourceModel\Segments\Products::class);
    }

    /**
     * @param \Magento\Framework\Data\Collection\AbstractDb $collection
     *
     * @return \Magento\Framework\Data\Collection\AbstractDb
     */
    public function addToCollection(\Magento\Framework\Data\Collection\AbstractDb $collection)
    {

        $customerSegments = $this->pandaHelper->getCustomerSegmentsIds();
        $connection = $collection->getResource()->getConnection();

        $parts = $collection->getSelect()->getPart('from');

        if (isset($parts['e']['tableName']) &&
            isset($parts['e']['tableName']) == $collection->getResource()->getTable('catalog_product_entity')) {

            $allCatalogs = $connection->fetchCol(
                $connection->select()
                           ->from($collection->getResource()->getTable('panda_segments'), ['segment_id'])
                           ->where('manual=?', 1)
            );

            $collection->getSelect()
                       ->joinLeft(
                           ['p' => $collection->getResource()->getTable('panda_segments_products')],
                           'e.entity_id = p.product_id'
                           , ['segment_id']
                       );

            if ($customerSegments) {
                $collection->getSelect()->where('p.segment_id IS NULL OR p.segment_id IN (?)', $customerSegments);
            } else {
                $collection->getSelect()->where('p.segment_id IS NULL OR p.segment_id NOT IN (?)', $allCatalogs);
            }

        }

        return $collection;
    }

}
