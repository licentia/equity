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
 * @modified   30/01/20, 13:26 GMT
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