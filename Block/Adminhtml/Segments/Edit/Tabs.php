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

namespace Licentia\Equity\Block\Adminhtml\Segments\Edit;

/**
 * Class Tabs
 *
 * @package Licentia\Panda\Block\Adminhtml\Segments\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected \Magento\Framework\Registry $registry;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context  $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session      $authSession
     * @param \Magento\Framework\Registry              $coreRegistry
     * @param array                                    $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {

        $this->registry = $coreRegistry;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    public function _construct()
    {

        parent::_construct();
        $this->setId("panda_tabs");
        $this->setDestElementId("edit_form");
        $this->setTitle(__("Segment Information"));
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _beforeToHtml()
    {

        /** @var \Licentia\Equity\Model\Segments $segment */
        $segment = $this->registry->registry('panda_segment');

        $this->addTab(
            "main_section",
            [
                "label"   => __("General"),
                "title"   => __("General"),
                'content' => $this->getLayout()
                                  ->createBlock('Licentia\Equity\Block\Adminhtml\Segments\Edit\Tab\Main')
                                  ->toHtml(),
            ]
        );

        if ($segment->getId()) {
            $this->addTab(
                "conditions_section",
                [
                    "label"   => __("Conditions"),
                    "title"   => __("Conditions"),
                    'content' => $this->getLayout()
                                      ->createBlock('Licentia\Equity\Block\Adminhtml\Segments\Edit\Tab\Conditions')
                                      ->toHtml(),
                ]
            );
        }

        if ($segment->getId()) {
            $this->addTab(
                "records_section",
                [
                    "label" => __("Records"),
                    "title" => __("Records"),
                    'class' => 'ajax',
                    'url'   => $this->getUrl('*/*/recordsgrid', ['_current' => true]),
                ]
            );
        }

        if ($segment->getId()) {
            $this->addTab(
                "evolution_section",
                [
                    "label" => __("Records Evolution"),
                    "title" => __("Records Evolution"),
                    'class' => 'ajax',
                    'url'   => $this->getUrl('*/*/evolutiongrid', ['_current' => true]),
                ]
            );
        }

        if ($segment->getUseAsCatalog()) {

            $this->addTab(
                "products_section",
                [
                    "label" => __("Products Assigned"),
                    "title" => __("Products Assigned"),
                    'class' => 'ajax',
                    'url'   => $this->getUrl('*/*/productsgrid', ['_current' => true]),
                ]
            );
        }

        return parent::_beforeToHtml();
    }
}
