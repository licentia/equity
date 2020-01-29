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

namespace Licentia\Equity\Block\Adminhtml;

/**
 * Class Templates
 *
 * @package Licentia\Panda\Block\Adminhtml
 */
class Access extends \Magento\Backend\Block\Widget\Container
{

    /**
     *
     */
    protected function _construct()
    {

        parent::_construct();

        $this->_blockGroup = 'Licentia_Equity';
        $this->_controller = 'adminhtml_access';
        $this->_headerText = __('Access');
    }

    /**
     * @return \Magento\Backend\Block\Widget\Container
     */
    protected function _prepareLayout()
    {

        $addButtonProps = [
            'id'           => 'add_new_access',
            'label'        => __('Select Entity to restrict access to'),
            'class'        => 'add',
            'button_class' => '',
            'class_name'   => 'Magento\Backend\Block\Widget\Button\SplitButton',
            'options'      => $this->_getAddProductButtonOptions(),
        ];
        $this->buttonList->add('add_new', $addButtonProps);

        return parent::_prepareLayout();
    }

    /**
     * Get dropdown options for save split button
     *
     * @return array
     */
    protected function _getAddProductButtonOptions()
    {

        $options = [];

        $types = \Licentia\Equity\Model\Access::getAccessTypes();

        foreach ($types as $key => $store) {
            $options[] = [
                'id'      => 'edit-button',
                'label'   => __($store),
                'onclick' => "window.location='" . $this->getUrl(
                        '*/*/new',
                        [
                            'entity_type' => $key,
                        ]
                    ) . "'",
                'default' => false,
            ];
        }

        return $options;
    }
}
