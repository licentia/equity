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

namespace Licentia\Equity\Block\Adminhtml\System\Config\Form\Field;

/**
 * Class Formulas
 *
 * @package Licentia\Panda\Block\Adminhtml\System\Config\Form\Field
 */
class Formulas extends \Magento\Config\Block\System\Config\Form\Field
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
        $url = $this->getUrl('pandae/formulas/testFormulas');

        return $js . '<style type="text/css">#row_panda_prices_products_help > td.label, #row_panda_prices_customers_help > td.label > label {visibility:hidden} #row_panda_prices_test_formulas td.label label{display:none;}</style>
           <br><br><span><span><span> <button  onclick="PopupCenter(\'' . $url . '\',\'Panda\',\'820\',\'850\')" class="scalable" type="button" >
            ' . __('Open Product Pricing Formula Tester') . '</button></span></span></span><br><br><br><br>';
    }
}
