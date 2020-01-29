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

namespace Licentia\Equity\Block\Adminhtml\Formulas\Edit\Tab;

/**
 * Class Form
 *
 * @package Licentia\Panda\Block\Adminhtml\Formulas\Edit\Tab
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Magento\Store\Model\System\Store       $systemStore
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {

        $this->systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {

        parent::_construct();
        $this->setId('block_form');
        $this->setTitle(__('Block Information'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {

        $current = $this->_coreRegistry->registry('panda_formula');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id'     => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                ],
            ]
        );

        $form->setHtmlIdPrefix('type_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            "cron",
            "select",
            [
                "label"   => __("Auto Update Options"),
                "options" => [
                    '0' => __('No Update'),
                    'd' => __('Update Daily'),
                    'w' => __('Update Weekly'),
                    'm' => __('Update Monthly'),
                ],
                "name"    => "cron",
            ]
        );

        $fieldset1 = $form->addFieldset(
            'loyal_fieldset',
            ['legend' => __('Customers Loyalty'), 'class' => 'fieldset-wide']
        );

        $fieldset1->addField(
            "formula_0",
            "text",
            [
                "label" => __("Customer Loyalty Formula"),
                "name"  => "formula_0",
                "note"  => __(
                    'If the result of this formula is true, the customer will be treated as "loyal". Check the "Help with Formulas" tab on the left for help'
                ),
            ]
        );

        for ($i = 1; $i <= \Licentia\Equity\Model\Formulas::TOTAL_FORMULAS; $i++) {
            $fieldset[$i] = $form->addFieldset(
                "panda_form_" . $i,
                ["legend" => __("Customer Equity: Formula $i"), 'class' => 'fieldset-wide']
            );

            $fieldset[$i]->addField(
                "formula_" . $i . '_name',
                "text",
                [
                    "label"    => __("Formula %1 Name ", $i),
                    "name"     => "formula_" . $i . '_name',
                    "required" => true,
                    "class"    => 'required',
                ]
            );

            $fieldset[$i]->addField(
                "formula_" . $i,
                "textarea",
                [
                    "label" => __("Formula %1 Code ", $i),
                    "style" => "height:75px",
                    "name"  => "formula_" . $i,
                    "note"  => __(
                        'Please always use operation signs:<br><br>[Wrong]<br>3 ( {number_orders} * {order_amount} )<br><br>[OK]<br>3 * ( {number_orders} * {order_amount} )'
                    ),
                ]
            );
        }

        $this->setForm($form);

        if ($current) {
            $currentValues = $current->getData();
            $form->addValues($currentValues);
        }

        return parent::_prepareForm();
    }
}
