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

namespace Licentia\Equity\Block\Adminhtml\Segments\Edit\Tab;

/**
 * Class Main
 *
 * @package Licentia\Panda\Block\Adminhtml\Segments\Edit\Tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic
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
     * @return $this
     */
    protected function _prepareForm()
    {

        $current = $this->_coreRegistry->registry("panda_segment");

        $disabled = false;
        if ($current->getId() && $current->getManual() == 1) {
            $disabled = true;
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

        $fieldset->addField(
            "name",
            "text",
            [
                "label"    => __("Segment Name"),
                "class"    => "required-entry",
                "required" => true,
                "name"     => "name",
            ]
        );

        $fieldset->addField(
            "is_active",
            "select",
            [
                "label"    => __("Is Active"),
                "options"  => ['1' => __('Yes'), '0' => __('No')],
                "required" => true,
                "name"     => "is_active",
            ]
        );
        $html = '
                <script type="text/javascript">

                require(["jquery"],function ($){

                toggleControlsValidateType = {
                    run: function() {
                        if($("#manual").val() == "1" ){
                                $("div.admin__field.field.field-type").hide();
                                $("div.admin__field.field.field-cron").hide();
                                $("div.admin__field.field.field-websites_ids").hide();
                                $("#websites_ids").removeClass("required-entry");
                         }else{
                                $("div.admin__field.field.field-type").show();
                                $("div.admin__field.field.field-cron").show();
                                $("div.admin__field.field.field-websites_ids").show();
                                $("#websites_ids").addClass("required-entry");
                        }
                    }
                }
                window.toggleControlsValidateType = toggleControlsValidateType;
                $(function() {
                    toggleControlsValidateType.run();
                });

                });
                </script>
                ';

        $fieldset->addField(
            "manual",
            "select",
            [
                "label"    => __("Manually Managed"),
                "options"  => ['1' => __('Yes'), '0' => __('No')],
                "name"     => "manual",
                "disabled" => $current->getId() ? true : false,
                "onchange" => "toggleControlsValidateType.run()",
                "note"     => __(
                    'If set to yes, only customers manually assigned will be part of this segment. Cannot be changed after.'
                ),
            ]
        )
                 ->setAfterElementHtml($html);

        if (!$disabled) {
            $fieldset->addField(
                "type",
                "select",
                [
                    "label"    => __("Segment Type"),
                    "options"  => [
                        'customers' => __('Registered Customers'),
                        'both'      => __('Registered Customers and Guest Users'),
                    ],
                    "name"     => "type",
                    "note"     => __(
                        "Please save this information in order to specify the conditions for segmentation. Cannot be changed after."
                    ),
                    "disabled" => $current->getId() ? true : false,
                ]
            );

            $fieldset->addField(
                "websites_ids",
                "multiselect",
                [
                    "class"    => "required-entry",
                    "required" => true,
                    "label"    => __('Website Scope'),
                    "values"   => $this->systemStore->getWebsiteValuesForForm(),
                    "name"     => "websites_ids[]",
                ]
            );
        }

        $fieldset->addField(
            "description",
            "textarea",
            [
                "label" => __("Description"),
                "name"  => "description",
            ]
        );

        $fieldset->addField(
            "products_relations",
            "select",
            [
                "label"   => __("Build Reports for This Segment?"),
                "options" => ['1' => __('Yes'), '0' => __('No')],
                "name"    => "products_relations",
            ]
        );

        if (!$disabled) {
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
                        'r' => __('Real Time Update'),
                    ],
                    "name"    => "cron",
                ]
            );
        }

        $fieldset->addField(
            "use_in_pricing",
            "select",
            [
                "label"    => __("Enable Product Pricing for segment?"),
                "options"  => ['1' => __('Yes'), '0' => __('No')],
                "required" => true,
                "name"     => "use_in_pricing",
            ]
        );

        $fieldset->addField(
            "use_in_acl",
            "select",
            [
                "label"    => __("Use to Restrict Access to Pages/Products/Categories"),
                "options"  => ['1' => __('Yes'), '0' => __('No')],
                "required" => true,
                "name"     => "use_in_acl",
            ]
        );

        $this->setForm($form);

        if ($current) {
            $currentValues = $current->getData();
            $form->setValues($currentValues);
        }

        return parent::_prepareForm();
    }
}
