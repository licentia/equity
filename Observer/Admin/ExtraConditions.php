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

namespace Licentia\Equity\Observer\Admin;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class ExtraConditions
 *
 * @package Licentia\Panda\Observer
 */
class ExtraConditions implements ObserverInterface
{

    /**
     * @param \Magento\Framework\Event\Observer $event
     */
    public function execute(\Magento\Framework\Event\Observer $event)
    {

        $conditions = $event->getEvent()->getAdditional();
        $attributes = [];
        $attributes[] = [
            'value' => 'Licentia\Equity\Model\Rule\Condition\Segment|exists',
            'label' => __('Customer Segment'),
        ];

        $conditions1 = [['label' => __('Customer Segment'), 'value' => $attributes]];

        $conditions->setConditions($conditions1);
    }
}
