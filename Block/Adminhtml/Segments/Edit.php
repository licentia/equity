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

namespace Licentia\Equity\Block\Adminhtml\Segments;

/**
 * Class Edit
 *
 * @package Licentia\Panda\Block\Adminhtml\Segments
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

    public function _construct()
    {

        parent::_construct();
        $this->_objectId = "id";
        $this->_blockGroup = "Licentia_Equity";
        $this->_controller = "adminhtml_segments";

        $this->buttonList->add(
            'save_and_continue',
            [
                'label'          => __('Save and Continue Edit'),
                'class'          => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                    ],
                ],
            ],
            10
        );
        $segment = $this->registry->registry('panda_segment');

        if ($segment->getId() && $segment->getData('is_active') == 1) {
            $url = $this->getUrl(
                '*/segments/records',
                ['refresh' => 1, 'id' => $segment->getId()]
            );
            $text = __('This will refresh your segment next time your cron runs. Continue?');

            $this->buttonList->add(
                'background_refresh',
                [
                    'label' => __('Refresh Records'),
                    'class' => '',
                ]
            );

            $this->buttonList->update(
                'background_refresh',
                'onclick',
                "if(!confirm('$text')){return false;}; window.location='$url'"
            );
        }
    }

    /**
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {

        return $this->getUrl(
            '*/*/save',
            ['_current' => true, 'back' => 'edit', 'tab' => '{{tab_id}}']
        );
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {

        if ($this->registry->registry("panda_segment")
                           ->getId()) {
            return __(
                "Edit Segment '%s'",
                $this->htmlEscape(
                    $this->registry->registry("panda_segment")
                                   ->getName()
                )
            );
        } else {
            return __("Add Segment");
        }
    }
}
