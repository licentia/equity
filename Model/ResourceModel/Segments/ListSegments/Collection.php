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

namespace Licentia\Equity\Model\ResourceModel\Segments\ListSegments;

/**
 * Class Collection
 *
 * @package Licentia\Panda\Model\ResourceModel\Segments\ListSegments
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * Collection constructor.
     *
     * @param \Magento\Framework\Indexer\IndexerRegistry                   $indexer
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory
     * @param \Licentia\Equity\Logger\Logger                               $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null          $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null    $resource
     */
    public function __construct(
        \Magento\Framework\Indexer\IndexerRegistry $indexer,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Licentia\Equity\Logger\Logger $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {

        $this->indexerRegistry = $indexer;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Constructor
     * Configures collection
     *
     * @return void
     */
    protected function _construct()
    {

        parent::_construct();
        $this->_init(
            \Licentia\Equity\Model\Segments\ListSegments::class,
            \Licentia\Equity\Model\ResourceModel\Segments\ListSegments::class
        );
    }

    /**
     * @return $this
     */
    public function delete()
    {

        foreach ($this->getItems() as $k => $item) {
            $item->delete();
            unset($this->_items[$k]);
        }

        return $this;
    }

    /**
     * @param string $valueField
     * @param string $labelField
     *
     * @return array
     */
    protected function _toOptionHash($valueField = null, $labelField = 'name')
    {

        return parent::_toOptionHash('segment_id', 'segment_id');
    }

    /**
     * @param bool $field
     *
     * @return array
     */
    public function getAllIds($field = false)
    {

        if (!$field) {
            return parent::getAllIds();
        }

        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(\Zend_Db_Select::ORDER);
        $idsSelect->reset(\Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(\Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(\Zend_Db_Select::COLUMNS);
        $idsSelect->columns($field, 'main_table');

        return $this->getConnection()->fetchCol($idsSelect);
    }
}
