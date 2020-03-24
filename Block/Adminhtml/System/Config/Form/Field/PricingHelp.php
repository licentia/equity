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
 * @modified   18/03/20, 05:31 GMT
 *
 */

namespace Licentia\Equity\Block\Adminhtml\System\Config\Form\Field;

/**
 * Class PricingHelp
 *
 * @package Licentia\Equity\Block\Adminhtml\System\Config\Form\Field
 */
class PricingHelp extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element
    ) {

        $js = '<script type="text/javascript">function PopupCenter(url, title, w, h) {
    // Fixes dual-screen position                         Most browsers      Firefox
    var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
    var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

    var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    var left = ((width / 2) - (w / 2)) + dualScreenLeft;
    var top = ((height / 2) - (h / 2)) + dualScreenTop;
    var newWindow = window.open(url, title, \'scrollbars=yes, width=\' + w + \', height=\' + h + \', top=\' + top + \', left=\' + left);

    // Puts focus on the newWindow
    if (window.focus) {
        newWindow.focus();
    }
}</script>';
        $url = $this->getUrl('pandae/formulas/help');

        return $js . '<style type="text/css">#row_panda_prices_test_formulas td.label label{display:none;}</style>
           <span><span><span> <button  onclick="PopupCenter(\'' . $url . '\',\'Panda\',\'820\',\'850\')" class="scalable" type="button" >
            ' . __('Open Pricing Helper') . '</button></span></span></span>';
    }
}