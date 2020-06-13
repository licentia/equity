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

namespace Licentia\Equity\Block\Adminhtml\Segments;

/**
 * Class Grid
 *
 * @package Licentia\Panda\Block\Adminhtml\Segments
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var  \Licentia\Equity\Model\ResourceModel\Segments\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context                         $context
     * @param \Magento\Backend\Helper\Data                                    $backendHelper
     * @param \Licentia\Equity\Model\ResourceModel\Segments\CollectionFactory $collectionFactory
     * @param array                                                           $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Equity\Model\ResourceModel\Segments\CollectionFactory $collectionFactory,
        array $data = []
    ) {

        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    public function _construct()
    {

        parent::_construct();
        $this->setId('importerGrid');
        $this->setDefaultSort('segment_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {

        $collection = $this->collectionFactory->create();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {

        $this->addColumn(
            'segment_id',
            [
                'header' => __('ID'),
                'width'  => '50px',
                'index'  => 'segment_id',
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'align'  => 'left',
                'index'  => 'name',
            ]
        );

        $this->addColumn(
            'type',
            [
                'header'  => __('Type'),
                'align'   => 'right',
                'width'   => '120px',
                'index'   => 'type',
                'type'    => 'options',
                'options' => [
                    'customers' => __('Customers'),
                    'both'      => __('Customers & Guests'),
                ],
            ]
        );

        $this->addColumn(
            'records',
            [
                'header' => __('Records'),
                'type'   => 'number',
                'width'  => '120px',
                'index'  => 'records',
            ]
        );

        $this->addColumn(
            'is_active',
            [
                'header'  => __('Active'),
                'align'   => 'right',
                'index'   => 'is_active',
                'type'    => 'options',
                'options' => [
                    1 => __('Yes'),
                    0 => __('No'),
                ],
            ]
        );

        $this->addColumn(
            'manual',
            [
                'header'  => __('Manually Managed'),
                'align'   => 'right',
                'width'   => '120px',
                'index'   => 'manual',
                'type'    => 'options',
                'options' => [
                    1 => __('Yes'),
                    0 => __('No'),
                ],
            ]
        );

        $this->addColumn(
            'cron',
            [
                'header'  => __('Update'),
                'align'   => 'right',
                'width'   => '80px',
                'index'   => 'cron',
                'type'    => 'options',
                'options' => [
                    '0' => __('No'),
                    'd' => __('Daily'),
                    'w' => __('Weekly'),
                    'm' => __('Monthly'),
                ],
            ]
        );

        $this->addColumn(
            'last_update',
            [
                'header' => __('Last Update'),
                'type'   => 'datetime',
                'width'  => '170px',
                'index'  => 'last_update',
            ]
        );

        $this->addColumn(
            'manually_added',
            [
                'header' => __('Manually Added'),
                'type'   => 'number',
                'width'  => '80px',
                'index'  => 'manually_added',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {

        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {

        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }
}
