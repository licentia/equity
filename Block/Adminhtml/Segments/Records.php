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

namespace Licentia\Equity\Block\Adminhtml\Segments;

/**
 * Class Records
 *
 * @package Licentia\Panda\Block\Adminhtml\Segments
 */
class Records extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry           $registry
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {

        $this->_blockGroup = 'Licentia_Equity';
        $this->_controller = 'adminhtml_segments_records';
        $this->_headerText = __('Segments');

        $this->buttonList->remove('add');

        if ($segment = $this->registry->registry('panda_segment')) {
            $this->_headerText = $segment->getname() . ' / ' . __('Segments');

            $urlBack = $this->getUrl('*/*/');
            $this->buttonList->add(
                'back',
                [
                    'label'   => __('Back'),
                    'class'   => 'back',
                    'onclick' => "window.location='$urlBack'",
                ]
            );

            $evolutionsUrl = $this->getUrl('pandae/segments/evolution', ['id' => $segment->getId()]);
            $this->buttonList->add(
                'evolutions_button',
                [
                    'label'   => __('Evolution'),
                    'onclick' => "window.location='$evolutionsUrl';",
                ]
            );

            $data = [
                'label'   => __('Edit Segment'),
                'class'   => '',
                'onclick' => "setLocation('{$this->getUrl("*/*/edit", ['_current' => true])}')",
            ];
            $this->buttonList->add('view_records', $data);

            $url = $this->getUrl(
                '*/segments/records',
                ['refresh' => 1, 'id' => $segment->getId()]
            );
            $text = __('This will refresh your segment next time your cron runs. Continue?');

            $this->buttonList->add(
                'background_refresh',
                [
                    'label' => __('Refresh Segment'),
                    'class' => 'save',
                ]
            );

            $this->buttonList->update(
                'background_refresh',
                'onclick',
                "if(!confirm('$text')){return false;}; window.location='$url'"
            );
        }

        parent::_construct();
    }
}
