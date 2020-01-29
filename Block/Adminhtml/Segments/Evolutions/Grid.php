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

namespace Licentia\Equity\Block\Adminhtml\Segments\Evolutions;

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
     * @param \Magento\Backend\Block\Template\Context                           $context
     * @param \Magento\Backend\Helper\Data                                      $backendHelper
     * @param \Magento\Framework\Registry                                       $registry
     * @param \Licentia\Equity\Model\ResourceModel\Evolutions\CollectionFactory $collectionFactory
     * @param \Licentia\Equity\Model\SegmentsFactory                            $segmentsFactory
     * @param array                                                             $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        \Licentia\Equity\Model\ResourceModel\Evolutions\CollectionFactory $collectionFactory,
        \Licentia\Equity\Model\SegmentsFactory $segmentsFactory,
        array $data = []
    ) {

        parent::__construct($context, $backendHelper, $data);

        $this->registry = $registry;
        $this->collectionFactory = $collectionFactory;
        $this->segmentsFactory = $segmentsFactory;
    }

    public function _construct()
    {

        parent::_construct();
        $this->setId('evolutions_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {

        $collection = $this->collectionFactory->create();

        if ($segment = $this->registry->registry('panda_segment')) {
            $collection->addFieldToFilter('segment_id', $segment->getId());
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {

        if (!$this->registry->registry('panda_segment')) {
            $this->addColumn(
                'segment_id',
                [
                    'header'  => __('Segment Name'),
                    'align'   => 'left',
                    'index'   => 'segment_id',
                    'type'    => 'options',
                    'options' => $this->segmentsFactory->create()
                                                       ->toFormValues(),
                ]
            );
        }

        $this->addColumn(
            'created_at',
            [
                'header' => __('Created at'),
                'align'  => 'left',
                'type'   => 'date',
                'index'  => 'created_at',
            ]
        );

        $this->addColumn(
            'records',
            [
                'header' => __('Total Records'),
                'align'  => 'left',
                'type'   => 'number',
                'index'  => 'records',
            ]
        );

        $this->addColumn(
            'variation',
            [
                'header' => __('Variation'),
                'align'  => 'left',
                'type'   => 'number',
                'index'  => 'variation',
            ]
        );

        $this->addExportType('*/*/exportEvoCsv', __('CSV'));
        $this->addExportType('*/*/exportEvoXml', __('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {

        $this->getMassactionBlock()->setTemplate('Licentia_Equity::widget/grid/massaction_extended.phtml');

        $this->setMassactionIdField('evolution_id');
        $this->getMassactionBlock()->setFormFieldName('ids');

        $this->getMassactionBlock()
             ->addItem(
                 'Delete',
                 [
                     'label'   => __('Delete'),
                     'url'     => $this->getUrl('*/*/deleteevo', ['_current' => true]),
                     'confirm' => __('Are you sure?'),
                 ]
             );

        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {

        return $this->getUrl('*/*/evolutiongrid', ['_current' => true]);
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
