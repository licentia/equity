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

namespace Licentia\Equity\Block\Adminhtml\System\Config\Form\Field;

/**
 * Class Clear
 *
 * @package Licentia\Panda\Block\Adminhtml\System\Config\Form\Field
 */
class Prices extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {

        $url = $this->getUrl('pandae/segments/prices');
        $message = __('Are you sure?');

        return '<style type="text/css">#row_panda_prices_clear_clear_prices > td.label > label  {display:none;}</style><br><br>' .
               '<button  onclick="if(!confirm(\'' . $message . '\')){return false;}' .
               'window.location=\'' . $url . '\'" class="scalable" type="button" >' .
               '<span><span><span>' . __('Clear Segment Prices') .
               '</span></span></span></button>';
    }
}
