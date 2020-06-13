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
 * Class Records
 *
 * @package Licentia\Panda\Block\Adminhtml\Segments
 */
class Evolutions extends \Magento\Backend\Block\Widget\Grid\Container
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

        $this->_controller = 'adminhtml_segments_evolutions';
        $this->_blockGroup = 'Licentia_Equity';
        $this->_headerText = __('Segments Evolutions');

        $this->buttonList->remove('add');

        if ($segment = $this->registry->registry('panda_segment')) {
            $this->_headerText = __('Segments Evolutions') . ' / ' . $segment->getName();

            $cancelUrl = $this->getUrl('pandae/segments/index', ['id' => $segment->getId()]);

            $this->buttonList->add(
                'cancel_campaign',
                [
                    'label'   => __('Back'),
                    'onclick' => "window.location='$cancelUrl';",
                    'class'   => 'back',
                ]
            );

            $recordsUrl = $this->getUrl('pandae/segments/records', ['id' => $segment->getId()]);
            $this->buttonList->add(
                'records_button',
                [
                    'label'   => __('Records'),
                    'onclick' => "window.location='$recordsUrl';",
                ]
            );

            $data = [
                'label'   => __('Edit Segment'),
                'class'   => '',
                'onclick' => "setLocation('{$this->getUrl("*/*/edit", ['_current' => true])}')",
            ];
            $this->buttonList->add('edit_segment', $data);

            $recordsDeleteUrl =
                $this->getUrl('pandae/segments/deleteevo', ['id' => $segment->getId()]);
            $text = __('Are you sure?');
            $this->buttonList->add(
                'delete_button',
                [
                    'class'   => 'delete',
                    'label'   => __('Delete ALL Records'),
                    'onclick' => "if(!confirm('$text')){return false;}; window.location='$recordsDeleteUrl'",
                ]
            );
        }

        parent::_construct();
    }
}
