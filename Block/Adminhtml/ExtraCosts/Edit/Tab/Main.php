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

namespace Licentia\Equity\Block\Adminhtml\ExtraCosts\Edit\Tab;

/**
 * Class Main
 *
 * @package Licentia\Panda\Block\Adminhtml\ExtraCosts\Edit\Tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Licentia\Equity\Model\Sales\ExtraCostsFactory
     */
    protected $extraCostsFactory;

    /**
     * @var \Licentia\Equity\Model\Source\ExtraCosts
     */
    protected $extraCostsSource;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Shipping\Model\Config
     */
    protected $shipconfig;

    /**
     * @var \Licentia\Panda\Model\Source\ShippingMethods
     */
    protected $shippingMethods;

    /**
     * @var \Licentia\Panda\Model\Source\PaymentMethods
     */
    protected $paymentMethods;

    /**
     * Main constructor.
     *
     * @param \Licentia\Panda\Model\Source\ShippingMethods       $shippingMethods
     * @param \Licentia\Panda\Model\Source\PaymentMethods        $paymentMethods
     * @param \Licentia\Equity\Model\Source\ExtraCosts           $extraCostsSource
     * @param \Magento\Backend\Block\Template\Context            $context
     * @param \Licentia\Equity\Model\Sales\ExtraCostsFactory     $extraCostsFactory
     * @param \Licentia\Equity\Helper\Data                       $pandaHelper
     * @param \Magento\Framework\Registry                        $registry
     * @param \Magento\Framework\Data\FormFactory                $formFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Shipping\Model\Config                     $shipconfig
     * @param array                                              $data
     */
    public function __construct(
        \Licentia\Panda\Model\Source\ShippingMethods $shippingMethods,
        \Licentia\Panda\Model\Source\PaymentMethods $paymentMethods,
        \Licentia\Equity\Model\Source\ExtraCosts $extraCostsSource,
        \Magento\Backend\Block\Template\Context $context,
        \Licentia\Equity\Model\Sales\ExtraCostsFactory $extraCostsFactory,
        \Licentia\Equity\Helper\Data $pandaHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Shipping\Model\Config $shipconfig,
        array $data = []
    ) {

        parent::__construct($context, $registry, $formFactory, $data);

        $this->extraCostsFactory = $extraCostsFactory;
        $this->pandaHelper = $pandaHelper;
        $this->extraCostsSource = $extraCostsSource;
        $this->shipconfig = $shipconfig;
        $this->scopeConfig = $scopeConfig;
        $this->shippingMethods = $shippingMethods;
        $this->paymentMethods = $paymentMethods;
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {

        /** @var \Licentia\Equity\Model\Sales\ExtraCosts $model */
        $model = $this->_coreRegistry->registry('panda_extra_cost');

        if ($this->getRequest()->getParam('entity_type')) {
            $model->setType($this->getRequest()->getParam('entity_type'));
        }
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

        $fieldset = $form->addFieldset('params_fieldset', ['legend' => __('Settings')]);

        if ($model->getType()) {
            $fieldset->addField('entity_type', 'hidden', ['value' => $model->getType(), 'name' => 'type']);
        }

        if ($model->getType() == 'shipping') {
            $model->setShippingMethods(explode(',', $model->getShippingMethods()));

            $fieldset->addField(
                "shipping_methods",
                "multiselect",
                [
                    "label"    => __("Shipping Method"),
                    "name"     => "shipping_methods",
                    "values"   => $this->shippingMethods->toOptionArray(),
                    'required' => true,
                ]
            );
        }
        if ($model->getType() == 'payments') {
            $model->setPaymentMethods(explode(',', $model->getPaymentMethods()));

            $fieldset->addField(
                "payment_methods",
                "multiselect",
                [
                    "label"    => __("Payment Methods"),
                    "name"     => "payment_methods",
                    "values"   => $this->paymentMethods->toOptionArray(),
                    'required' => true,
                ]
            );
        }

        $fieldset->addField(
            "title",
            "text",
            [
                "label"    => __("Title"),
                "class"    => "required-entry",
                "required" => true,
                "name"     => "title",
            ]
        );

        $fieldset->addField(
            "description",
            "textarea",
            [
                "label" => __("Description"),
                "name"  => "description",
            ]
        );

        $fieldset->addField(
            "investment",
            "text",
            [
                "label"    => __("Amount Invested"),
                "name"     => "investment",
                "class"    => "validate validate-number small_input",
                "note"     => "To be split among all matching orders",
                "required" => true,
            ]
        );

        if ($model->getType() == 'marketing') {
            $fieldset->addField(
                "target",
                "text",
                [
                    "label" => __("Sales Goal"),
                    "name"  => "target",
                    "note"  => "Informative purposes only",
                ]
            );
            $fieldset->addField(
                "campaign",
                "text",
                [
                    "label" => __("Campaign"),
                    "name"  => "campaign",
                    "note"  => __(
                        'Campaign name used in Ads. The value of the variable: utm_campaign.
                    <br>If you leave this field empty, the investment amount will be split trough all orders.
                    <br>Use "*" (without quotes) to apply only to orders originated in campaigns.
                    <br>Separate multiple campaigns names with a comma ,
                    <br>Eg. *facebook =>For orders with campaigns where the campaign name starts with facebook
                    <br>Eg. facebook* =>For orders with campaigns where the campaign name ends in facebook
                    <br>Eg. facebook,google =>For orders where the campaign name is facebook or google
                    <br>Eg. facebook,google* =>For orders where the campaign name is facebook or ends in google
                    <br>Eg. * =>For orders with any campaigns'
                    ),
                ]
            );
        } else {
            $fieldset->addField(
                "flat_fee",
                "text",
                [
                    "label"    => __("Flat Fee per Order"),
                    "name"     => "flat_fee",
                    "class"    => "validate validate-number small_input",
                    'required' => true,
                ]
            );

            $fieldset->addField(
                "variable_fee",
                "text",
                [
                    "label"    => __("Variable Fee per Order"),
                    "name"     => "variable_fee",
                    "class"    => "validate validate-number small_input",
                    'required' => true,
                    'note'     => "In percentile. Don't add the % ",
                ]
            );
        }

        $dateFormat = $this->_localeDate->getDateFormat();

        $fieldset->addField(
            'from_date',
            'date',
            [
                'name'        => 'from_date',
                'date_format' => $dateFormat,
                'label'       => __('Start Date'),
                'required'    => true,
            ]
        );

        $fieldset->addField(
            'to_date',
            'date',
            [
                'name'        => 'to_date',
                'date_format' => $dateFormat,
                'label'       => __('End Date'),
                'required'    => true,
            ]
        );

        $form->addValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
