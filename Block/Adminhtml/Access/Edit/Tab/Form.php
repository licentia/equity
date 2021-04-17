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

namespace Licentia\Equity\Block\Adminhtml\Access\Edit\Tab;

use Licentia\Equity\Model\SegmentsFactory;

/**
 * Class Form
 *
 * @package Licentia\Equity\Block\Adminhtml\Access\Edit\Tab
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var SegmentsFactory
     */
    protected $segmentsFactory;

    /**
     * Form constructor.
     *
     * @param SegmentsFactory                         $segmentsFactory
     * @param \Licentia\Equity\Helper\Data            $pandaHelper
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param array                                   $data
     */
    public function __construct(
        SegmentsFactory $segmentsFactory,
        \Licentia\Equity\Helper\Data $pandaHelper,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {

        parent::__construct($context, $registry, $formFactory, $data);

        $this->pandaHelper = $pandaHelper;
        $this->segmentsFactory = $segmentsFactory;
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {

        parent::_construct();
        $this->setId('block_form');
        $this->setTitle(__('Block Information'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {

        /** @var \Licentia\Equity\Model\Access $model */
        $model = $this->_coreRegistry->registry('panda_access');

        if ($this->getRequest()->getParam('entity_type')) {
            $model->setEntityType($this->getRequest()->getParam('entity_type'));
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id'     => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                ],
            ]
        );

        $form->setHtmlIdPrefix('access_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );

        if ($model->getEntityType()) {
            $fieldset->addField('entity_type', 'hidden', ['value' => $model->getEntityType(), 'name' => 'entity_type']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name'     => 'name',
                'label'    => __('Internal Name'),
                'title'    => __('Internal Name'),
                "required" => true,
            ]
        );

        $options = $this->segmentsFactory->create()->getOptionArray('Any');

        if (count($options) > 1) {
            $fieldset->addField(
                'segments_ids',
                'multiselect',
                [
                    'name'     => 'segments_ids[]',
                    'label'    => __('Segment'),
                    'title'    => __('Segment'),
                    'required' => true,
                    'values'   => $options,
                    "class"    => 'small_input',
                ]
            );
        }

        $entityType = ucwords($model->getEntityType());

        if ($model->getEntityType() == 'product') {
            $html = '
           <style type="text/css">
            .ui-autocomplete { position: absolute; cursor: default; background:#CCC }   
            html .ui-autocomplete { width:1px; }
            .ui-menu {
                list-style:none;
                padding: 2px;
                margin: 0;
                display:block;
                float: left;
            }
            .ui-menu .ui-menu {
                margin-top: -3px;
            }
            .ui-menu .ui-menu-item {
                margin:0;
                padding: 0;
                zoom: 1;
                float: left;
                clear: left;
                width: 100%;
            }
            .ui-menu .ui-menu-item a {
                text-decoration:none;
                display:block;
                padding:.2em .4em;
                line-height:1.5;
                zoom:1;
            }
            .ui-menu .ui-menu-item a.ui-state-hover,
            .ui-menu .ui-menu-item a.ui-state-active {
                font-weight: normal;
                margin: -1px;
            }
            .ui-helper-hidden-accessible{
            display:none;
            </style>
            <script type="text/javascript">
            
                require(["jquery", "jquery/ui"], function ($) {
            
                    $(function () {
                        $("#access_entity_id").autocomplete({
                            source: "' . $this->getUrl('*/ajax/search') . '",
                            minLength: 3
                        });
                    });
            
                });
            </script>';

            $fieldset->addField(
                'entity_id',
                'text',
                [
                    'name'     => 'entity_id',
                    'label'    => __($entityType . ' SKU'),
                    'title'    => __($entityType . ' SKU'),
                    "required" => true,
                    "class"    => 'small_input',
                ]
            )
                     ->setAfterElementHtml($html);
        }

        if ($model->getEntityType() == 'category') {
            $fieldset->addField(
                'entity_id',
                'select',
                [
                    'name'     => 'entity_id',
                    'label'    => __($entityType),
                    'title'    => __($entityType),
                    "required" => true,
                    "class"    => 'small_input',
                    'values'   => $this->pandaHelper->getCategories(),
                ]
            );
        }

        if ($model->getEntityType() == 'page') {
            $fieldset->addField(
                'entity_id',
                'select',
                [
                    'name'     => 'entity_id',
                    'label'    => __($entityType),
                    'title'    => __($entityType),
                    "required" => true,
                    "class"    => 'small_input',
                    'values'   => $this->pandaHelper->getCmsPages(),
                ]
            );
        }

        if ($model->getEntityType() == 'block') {
            $fieldset->addField(
                'entity_id',
                'select',
                [
                    'name'     => 'entity_id',
                    'label'    => __($entityType),
                    'title'    => __($entityType),
                    "required" => true,
                    "class"    => 'small_input',
                    'values'   => $this->pandaHelper->getCmsBlocks(),
                ]
            );
        }

        $fieldset->addField(
            "is_active",
            "select",
            [
                "label"    => __("Is Active"),
                "options"  => ['1' => __('Yes'), '0' => __('No')],
                "required" => true,
                "name"     => "is_active",
            ]
        );

        $dateFormat = $this->_localeDate->getDateFormat();

        $fieldset->addField(
            'from_date',
            'date',
            [
                'name'        => 'from_date',
                'date_format' => $dateFormat,
                'label'       => __('Active From Date'),
                "required"    => true,
            ]
        );

        $fieldset->addField(
            'to_date',
            'date',
            [
                'name'        => 'to_date',
                'date_format' => $dateFormat,
                'label'       => __('Active To Date'),
                "required"    => true,
            ]
        );

        $form->addValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
