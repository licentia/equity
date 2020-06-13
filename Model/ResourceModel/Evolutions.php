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

namespace Licentia\Equity\Model\ResourceModel;

/**
 * Class Evolutions
 *
 * @package Licentia\Panda\Model\ResourceModel
 */
class Evolutions extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * @var \Licentia\Equity\Model\ResourceModel\Segments\CollectionFactory
     */
    protected $segmentsCollection;

    /**
     * @var \Licentia\Equity\Model\Segments\ListSegmentsFactory
     */
    protected $listSegmentsFactory;

    /**
     * @var \Licentia\Equity\Model\ResourceModel\Segments\ListSegments\CollectionFactory
     */
    protected $listSegmentsCollection;

    /**
     * @var \Licentia\Equity\Model\EvolutionsFactory
     */
    protected $evolutionsFactory;

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Licentia\Equity\Model\ResourceModel\Evolutions\CollectionFactory
     */
    protected $evolutionsCollection;

    /**
     * Evolutions constructor.
     *
     * @param \Licentia\Equity\Model\EvolutionsFactory            $evolutionsFactory
     * @param Evolutions\CollectionFactory                        $evolutionsCollection
     * @param Segments\CollectionFactory                          $segmentsCollection
     * @param Segments\ListSegments\CollectionFactory             $listSegmentsCollection
     * @param \Licentia\Equity\Model\Segments\ListSegmentsFactory $listSegmentsFactory
     * @param \Licentia\Equity\Helper\Data                        $pandaHelper
     * @param \Magento\Framework\Model\ResourceModel\Db\Context   $context
     * @param null                                                $connectionName
     */
    public function __construct(
        \Licentia\Equity\Model\EvolutionsFactory $evolutionsFactory,
        \Licentia\Equity\Model\ResourceModel\Evolutions\CollectionFactory $evolutionsCollection,
        \Licentia\Equity\Model\ResourceModel\Segments\CollectionFactory $segmentsCollection,
        \Licentia\Equity\Model\ResourceModel\Segments\ListSegments\CollectionFactory $listSegmentsCollection,
        \Licentia\Equity\Model\Segments\ListSegmentsFactory $listSegmentsFactory,
        \Licentia\Equity\Helper\Data $pandaHelper,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null
    ) {

        parent::__construct($context, $connectionName);

        $this->segmentsCollection = $segmentsCollection;
        $this->listSegmentsFactory = $listSegmentsFactory;
        $this->listSegmentsCollection = $listSegmentsCollection;
        $this->pandaHelper = $pandaHelper;
        $this->evolutionsCollection = $evolutionsCollection;
        $this->evolutionsFactory = $evolutionsFactory;
    }

    /**
     * Initialize resource model
     * Get tablename from config
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init('panda_segments_evolutions', 'evolution_id');
    }

    /**
     * @throws \Exception
     */
    public function updateEvolutions()
    {

        $date = $this->pandaHelper->gmtDate('Y-m-d');
        $segments = $this->segmentsCollection->create();

        foreach ($segments as $segment) {
            $this->evolutionsCollection->create()
                                       ->addFieldToFilter('created_at', $date)
                                       ->addFieldToFilter('segment_id', $segment->getId())
                                       ->walk('delete');

            $previousRecords = $this->evolutionsCollection->create()
                                                          ->addFieldToFilter('segment_id', $segment->getId())
                                                          ->addFieldToFilter('created_at', ['lt' => $date])
                                                          ->setOrder('created_at', 'DESC')
                                                          ->setPageSize(1);

            $collection = $this->listSegmentsCollection->create()->addFieldToFilter('segment_id', $segment->getId());

            $evolution = [];
            $evolution['segment_id'] = $segment->getId();
            $evolution['created_at'] = $date;
            $evolution['records'] = $collection->count();

            if ($previousRecords->count() != 1) {
                $previousRecordsNumber = 0;
            } else {
                $pRecords = $previousRecords->getFirstItem();
                $previousRecordsNumber = $pRecords->getData('records');
            }

            $evolution['variation'] = $evolution['records'] - $previousRecordsNumber;

            $this->evolutionsFactory->create()
                                    ->setData($evolution)
                                    ->save();
        }
    }
}
