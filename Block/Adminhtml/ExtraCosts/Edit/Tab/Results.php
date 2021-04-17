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

namespace Licentia\Equity\Block\Adminhtml\ExtraCosts\Edit\Tab;

/**
 * Class Results
 *
 * @package Licentia\Panda\Block\Adminhtml\Campaigns\Edit\ExtraCosts
 */
class Results extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Archive\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected ?\Magento\Framework\Registry $registry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context                    $context
     * @param \Magento\Backend\Helper\Data                               $backendHelper
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry                                $registry
     * @param array                                                      $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        $this->registry = $registry;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    public function _construct()
    {

        parent::_construct();
        $this->setId('extraCosts_results_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {

        $current = $this->registry->registry('panda_extra_cost');

        $campaigns = explode(',', $current->getData('affected_orders'));
        $campaigns = array_map('trim', $campaigns);

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('entity_id', ['in' => $campaigns]);

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
            'entity_id',
            [
                'header' => __('Order ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'entity_id',
            ]
        );

        $this->addColumn(
            'base_grand_total',
            [
                'header'        => __('Order Amount'),
                'align'         => 'left',
                'index'         => 'base_grand_total',
                'type'          => 'currency',
                'currency_code' => $this->_storeManager->getStore()->getDefaultCurrencyCode(),
            ]
        );

        $this->addColumn(
            'customer_firstname',
            [
                'header' => __('Customer First Name'),
                'align'  => 'left',
                'index'  => 'customer_firstname',
            ]
        );

        $this->addColumn(
            'customer_lastname',
            [
                'header' => __('Customer Last Name'),
                'align'  => 'left',
                'index'  => 'customer_lastname',
            ]
        );

        $this->addColumn(
            'customer_email',
            [
                'header' => __('Customer Email'),
                'align'  => 'left',
                'index'  => 'customer_email',
            ]
        );

        $this->addColumn(
            'total_qty_ordered',
            [
                'header' => __('Qty Ordered'),
                'align'  => 'left',
                'type'   => 'number',
                'index'  => 'total_qty_ordered',
            ]
        );

        $this->addColumn(
            'created_at',
            [
                'header'    => __('Order Date'),
                'align'     => 'left',
                'index'     => 'created_at',
                'width'     => '170px',
                'type'      => 'datetime',
                'gmtoffset' => true,
            ]
        );

        $this->addColumn(
            'panda_extra_costs',
            [
                'header'        => __('Imputed Cost'),
                'align'         => 'left',
                'index'         => 'panda_extra_costs',
                'type'          => 'currency',
                'currency_code' => $this->_storeManager->getStore()->getDefaultCurrencyCode(),
            ]
        );

        $this->addColumn(
            'panda_acquisition_campaign',
            [
                'header' => __('Campaign'),
                'align'  => 'left',
                'index'  => 'panda_acquisition_campaign',
            ]
        );

        $this->addColumn(
            'action',
            [
                'header'         => __('Order'),
                'align'          => 'center',
                'frame_callback' => [$this, 'customerResult'],
                'system'         => true,
                'filter'         => false,
                'sortable'       => false,
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
     * @param $value
     *
     * @param $row
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function customerResult($value, $row)
    {

        $url = $this->getUrl('sales/order/view', ['order_id' => $row->getData('entity_id')]);

        return '<a href="' . $url . '">' . __('View') . '</a>';
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $item
     *
     * @return bool
     */
    public function getRowUrl($item)
    {

        return false;
    }
}
