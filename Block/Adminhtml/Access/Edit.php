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

namespace Licentia\Equity\Block\Adminhtml\Access;

/**
 * Class Edit
 *
 * @package Licentia\Panda\Block\Adminhtml\Access
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected ?\Magento\Framework\Registry $registry = null;

    /**
     * @var \Licentia\Equity\Model\AccessFactory
     */
    protected \Licentia\Equity\Model\AccessFactory $accessFactory;

    /**
     * @param \Licentia\Equity\Model\AccessFactory  $accessFactory
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry           $registry
     * @param array                                 $data
     */
    public function __construct(
        \Licentia\Equity\Model\AccessFactory $accessFactory,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        $this->accessFactory = $accessFactory;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {

        $this->_blockGroup = 'Licentia_Equity';
        $this->_controller = 'adminhtml_access';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Access'));
        $this->buttonList->update('delete', 'label', __('Delete Access'));

        $this->buttonList->remove('save');
        $this->getToolbar()
             ->addChild(
                 'save-split-button',
                 'Magento\Backend\Block\Widget\Button\SplitButton',
                 [
                     'id'           => 'save-split-button',
                     'label'        => __('Save'),
                     'class_name'   => 'Magento\Backend\Block\Widget\Button\SplitButton',
                     'button_class' => 'widget-button-update',
                     'options'      => [
                         [
                             'id'             => 'save-button',
                             'label'          => __('Save'),
                             'default'        => true,
                             'data_attribute' => [
                                 'mage-init' => [
                                     'button' => [
                                         'event'  => 'saveAndContinueEdit',
                                         'target' => '#edit_form',
                                     ],
                                 ],
                             ],
                         ],
                         [
                             'id'             => 'save-continue-button',
                             'label'          => __('Save & Close'),
                             'data_attribute' => [
                                 'mage-init' => [
                                     'button' => [
                                         'event'  => 'save',
                                         'target' => '#edit_form',
                                     ],
                                 ],
                             ],
                         ],
                     ],
                 ]
             );
    }

    /**
     * Get edit form container header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {

        $access = $this->registry->registry('current_access');

        if ($access->getId()) {
            $extra = '';
            if ($access->getParentId()) {
                $temp = $this->accessFactory->create()->load($access->getParentId());
                $extra = ' { ' . __('Variation of ') . $temp->getName() . ' }';
            }

            return __($this->escapeHtml($access->getName()) . $extra);
        } else {
            if ($this->registry->registry('panda_access')
                               ->getId()) {
                return __(
                    "Edit Message Access '%1'",
                    $this->escapeHtml(
                        $this->registry->registry('panda_access')
                                       ->getName()
                    )
                );
            } else {
                return __("New Message Access");
            }
        }
    }
}
