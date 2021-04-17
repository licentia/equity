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

namespace Licentia\Equity\Block\Adminhtml\Segments\Records;

/**
 * Class Grid
 *
 * @package Licentia\Panda\Block\Adminhtml\Segments\Records
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var  \Licentia\Equity\Model\ResourceModel\Segments\ListSegments\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Country\Collection
     */
    protected $countryCollection;

    /**
     * @var \Magento\Directory\Model\Config\Source\AllregionFactory
     */
    protected $shippingFactory;

    /**
     * @var \Magento\Directory\Model\Config\Source\AllregionFactory
     */
    protected $regionFactory;

    /**
     * @var \Magento\Directory\Model\Config\Source\AllregionFactory
     */
    protected $paymentsFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context                                      $context
     * @param \Magento\Backend\Helper\Data                                                 $backendHelper
     * @param \Magento\Directory\Model\ResourceModel\Country\Collection                    $countryCollection
     * @param \Magento\Directory\Model\Config\Source\AllregionFactory                      $allregionFactory
     * @param \Magento\Payment\Model\Config\Source\AllmethodsFactory                       $paymentsFactory
     * @param \Magento\Shipping\Model\Config\Source\AllmethodsFactory                      $shippingFactory
     * @param \Magento\Framework\Registry                                                  $registry
     * @param \Licentia\Equity\Model\ResourceModel\Segments\ListSegments\CollectionFactory $collectionFactory
     * @param array                                                                        $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Directory\Model\ResourceModel\Country\Collection $countryCollection,
        \Magento\Directory\Model\Config\Source\AllregionFactory $allregionFactory,
        \Magento\Payment\Model\Config\Source\AllmethodsFactory $paymentsFactory,
        \Magento\Shipping\Model\Config\Source\AllmethodsFactory $shippingFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Equity\Model\ResourceModel\Segments\ListSegments\CollectionFactory $collectionFactory,
        array $data = []
    ) {

        parent::__construct($context, $backendHelper, $data);

        $this->registry = $registry;
        $this->collectionFactory = $collectionFactory;
        $this->countryCollection = $countryCollection;
        $this->regionFactory = $allregionFactory;
        $this->shippingFactory = $shippingFactory;
        $this->paymentsFactory = $paymentsFactory;
    }

    /**
     *
     */
    public function _construct()
    {

        parent::_construct();
        $this->setId('segments_records_grid');
        $this->setDefaultSort('record_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {

        $id = $this->getRequest()->getParam('id');

        $collection = $this->collectionFactory->create()->addFieldToFilter('segment_id', $id);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {

        $segment = $this->registry->registry('panda_segment');
        $extraData = (array) json_decode($segment->getExtraData(), true);

        $this->addColumn(
            'customer_id',
            [
                'header'  => __('Cust. ID'),
                'align'   => 'left',
                'default' => 'N/A',
                'type'    => 'number',
                'index'   => 'customer_id',
            ]
        );

        $this->addColumn(
            'firstname',
            [
                'header'    => __('Name'),
                'align'     => 'left',
                'index'     => 'customer_name',
                'separator' => ' ',
            ]
        );

        $this->addColumn(
            'email',
            [
                'header' => __('Email'),
                'align'  => 'left',
                'index'  => 'email',
            ]
        );

        $fields = isset($extraData['fields']) ? $extraData['fields'] : [];
        $type = isset($extraData['type']) ? $extraData['type'] : [];

        if (count($fields) < 10 && count($fields) > 0 && is_array($fields)) {
            for ($i = 0; $i < count($fields); $i++) {
                $a = $i + 1;

                $options = [
                    'header' => __($fields[$i]),
                    'align'  => 'left',
                    'index'  => 'data_' . $a,
                ];

                if (isset($type['type_' . $fields[$i]])) {
                    $infoType = $type['type_' . $fields[$i]];

                    if (stripos($infoType, 'number') !== false) {
                        $options['type'] = 'number';
                        $options['filter_index'] = new \Zend_Db_Expr('CAST(`data_' . $a . '` AS SIGNED)');
                    }
                    if (stripos($infoType, 'currency') !== false) {
                        $options['type'] = 'currency';
                        $options['filter_index'] = new \Zend_Db_Expr('CAST(`data_' . $a . '` AS SIGNED)');
                        $options['currency_code'] = $this->_storeManager->getStore()->getDefaultCurrencyCode();
                    }
                    if (stripos($infoType, 'options') !== false) {
                        $options['type'] = 'options';

                        $values = [];
                        if (stripos($fields[$i], (string) __('gender')) !== false) {
                            $values = ['male' => 'Male', 'female' => 'Female'];
                        } elseif (stripos($fields[$i], (string) __('Country')) !== false) {
                            $cList = $this->countryCollection->loadData()->toOptionArray(false);
                            $values = [];
                            foreach ($cList as $key => $value) {
                                $values[$value['value']] = $value['label'];
                            }
                        } elseif (stripos($fields[$i], (string) __('Region')) !== false) {
                            $cList = $this->regionFactory->create()->toOptionArray();

                            $values = [];
                            foreach ($cList as $key => $value) {
                                $values[$value['value']] = $value['label'];
                            }
                        } elseif (stripos($fields[$i], (string) __('Payment')) !== false) {
                            $cList = $this->paymentsFactory->create()->toOptionArray();

                            $values = [];
                            foreach ($cList as $key => $value) {
                                if (is_array($value['value'])) {
                                    foreach ($value['value'] as $value1) {
                                        $values[$value1['value']] = $value1['label'];
                                    }
                                } else {
                                    $values[$value['value']] = $value['label'];
                                }
                            }
                        } elseif (stripos($fields[$i], (string) __('Shipping')) !== false) {
                            $cList = $this->shippingFactory->create()->toOptionArray();

                            $values = [];
                            foreach ($cList as $key => $value) {
                                if (!is_array($value['value']) && strlen($value['value']) == 0) {
                                    continue;
                                }
                                if (is_array($value['value'])) {
                                    foreach ($value['value'] as $name => $optionsList) {
                                        $values[$optionsList['value']] = $value['label'] . '/' . $optionsList['label'];
                                    }
                                } else {
                                    $values[$value['value']] = $value['label'];
                                }
                            }
                        }

                        if (count($values) > 0) {
                            $options['options'] = $values;
                            $options['width'] = 200;
                        } else {
                            $options['type'] = 'text';
                        }
                    }
                }

                $this->addColumn('data_' . $a, $options);
            }
        }

        $this->addColumn(
            'customer_link',
            [
                'header'         => __('View'),
                'align'          => 'center',
                'index'          => 'customer_id',
                'width'          => '80px',
                'frame_callback' => [$this, 'customerResult'],
                'is_system'      => true,
                'sortable'       => false,
            ]
        );

        $this->addExportType('*/*/exportCsv', __('CSV'));
        $this->addExportType('*/*/exportXml', __('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {

        $this->getMassactionBlock()->setTemplate('Licentia_Equity::widget/grid/massaction_extended.phtml');

        $this->setMassactionIdField('record_id');
        $this->getMassactionBlock()->setFormFieldName('ids');

        $this->getMassactionBlock()
             ->addItem(
                 'massManualDelete',
                 [
                     'label'   => __('Mark as Auto Added'),
                     'url'     => $this->getUrl('*/*/massManualDelete', ['_current' => true]),
                     'confirm' => __('Are you sure?'),
                 ]
             );

        $this->getMassactionBlock()
             ->addItem(
                 'massManualAdd',
                 [
                     'label'   => __('Mark as Manually Added'),
                     'url'     => $this->getUrl('*/*/massManualAdd', ['_current' => true]),
                     'confirm' => __('Are you sure?'),
                 ]
             );

        $this->getMassactionBlock()
             ->addItem(
                 'massDelete',
                 [
                     'label'   => __('Remove'),
                     'url'     => $this->getUrl('*/*/massDelete', ['_current' => true]),
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

        return $this->getUrl('*/*/recordsgrid', ['_current' => true]);
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

    /**
     * @param $value
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function customerResult($value)
    {

        if ($value > 0) {
            $url = $this->getUrl('customer/index/edit/', ['id' => $value]);

            return '<a href="' . $url . '">' . __('Customer') . '</a>';
        }

        return __('N/A');
    }
}
