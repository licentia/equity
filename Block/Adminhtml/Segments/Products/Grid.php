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

namespace Licentia\Equity\Block\Adminhtml\Segments\Products;

/**
 * Class Grid
 *
 * @package Licentia\Panda\Block\Adminhtml\Segments\Evolutions
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Licentia\Equity\Model\ResourceModel\Evolutions\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Licentia\Equity\Model\SegmentsFactory
     */
    protected $segmentsFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollection;

    /**
     * Grid constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                                  $context
     * @param \Magento\Backend\Helper\Data                                             $backendHelper
     * @param \Magento\Framework\Registry                                              $registry
     * @param \Licentia\Equity\Model\ResourceModel\Segments\Products\CollectionFactory $collectionFactory
     * @param \Licentia\Equity\Model\SegmentsFactory                                   $segmentsFactory
     * @param array                                                                    $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        \Licentia\Equity\Model\ResourceModel\Segments\Products\CollectionFactory $collectionFactory,
        \Licentia\Equity\Model\SegmentsFactory $segmentsFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productColleciton,
        array $data = []
    ) {

        parent::__construct($context, $backendHelper, $data);

        $this->registry = $registry;
        $this->collectionFactory = $collectionFactory;
        $this->segmentsFactory = $segmentsFactory;
        $this->productCollection = $productColleciton;
    }

    public function _construct()
    {

        parent::_construct();
        $this->setId('products_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {

        $collection = $this->productCollection->create();
        $resource = $this->segmentsFactory->create()->getResource();
        $collection->getSelect()
                   ->joinInner(
                       ['p' => $resource->getTable('panda_segments_products')],
                       'e.entity_id = p.product_id'
                       , ['entity_id' => 'record_id']
                   );

        if ($segment = $this->registry->registry('panda_segment')) {
            $collection->getSelect()->where('segment_id=?', $segment->getId());
        }
        $collection->addAttributeToSelect('*');

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
            'name',
            [
                'header' => __('Name'),
                'align'  => 'left',
                'index'  => 'name',
            ]
        );

        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'align'  => 'left',
                'index'  => 'sku',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {

        return $this->getUrl('*/*/productsgrid', ['_current' => true]);
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {

        $this->getMassactionBlock()->setTemplate('Licentia_Equity::widget/grid/massaction_extended.phtml');

        $this->getMassactionBlock()->setFormFieldName('ids');
        $this->setMassactionIdField('entity_id');

        $this->getMassactionBlock()
             ->addItem(
                 'Delete',
                 [
                     'label'   => __('Delete'),
                     'url'     => $this->getUrl('*/*/deleteproducts', ['_current' => true]),
                     'confirm' => __('Are you sure?'),
                 ]
             );

        return $this;
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     *
     * @return bool
     */
    public function getRowUrl($row)
    {

        return false;
    }
}
