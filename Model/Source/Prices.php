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

namespace Licentia\Equity\Model\Source;

/**
 * Class Prices
 *
 * @package Licentia\Equity\Model\Source
 */
class Prices
{

    /**
     * @return array
     */
    public function toOptionArray()
    {

        $return = [];

        $return[] = [
            'value' => 'special_price',
            'label' => __('Special Price'),
        ];

        $return[] = [
            'value' => 'price',
            'label' => __('Final Price'),
        ];

        return $return;
    }
}
