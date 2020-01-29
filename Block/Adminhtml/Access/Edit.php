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
    protected $registry = null;

    /**
     * @var \Licentia\Equity\Model\AccessFactory
     */
    protected $accessFactory;

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
