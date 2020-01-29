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

namespace Licentia\Equity\Model\Segments\Condition;

use Magento\Rule\Model\Condition\Context;

/**
 * Class Subscriber
 *
 * @package Licentia\Equity\Model\Segments\Condition
 */
class Subscriber extends \Magento\Rule\Model\Condition\AbstractCondition
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Licentia\Panda\Model\ExtraFieldsFactory
     */
    protected $extraFieldsFactory;

    /**
     * @var \Licentia\Forms\Model\FormEntriesFactory
     */
    protected $formEntriesFactory;

    /**
     * @var \Licentia\Forms\Model\FormsFactory
     */
    protected $formsFactory;

    /**
     * @var \Licentia\Forms\Model\FormElementsFactory
     */
    protected $formElementsFactory;

    /**
     * Subscriber constructor.
     *
     * @param \Magento\Framework\Registry               $registry
     * @param \Magento\Store\Model\System\Store         $systemStore
     * @param \Licentia\Panda\Model\ExtraFieldsFactory  $extraFieldsFactory
     * @param \Licentia\Forms\Model\FormEntriesFactory  $formEntriesFactory
     * @param \Licentia\Forms\Model\FormElementsFactory $formElementsFactory
     * @param \Licentia\Forms\Model\FormsFactory        $formsFactory
     * @param Context                                   $context
     * @param array                                     $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\System\Store $systemStore,
        \Licentia\Panda\Model\ExtraFieldsFactory $extraFieldsFactory,
        \Licentia\Forms\Model\FormEntriesFactory $formEntriesFactory,
        \Licentia\Forms\Model\FormElementsFactory $formElementsFactory,
        \Licentia\Forms\Model\FormsFactory $formsFactory,
        Context $context,
        array $data = []
    ) {

        $this->registry = $registry;

        $this->formElementsFactory = $formElementsFactory;
        $this->formEntriesFactory = $formEntriesFactory;
        $this->extraFieldsFactory = $extraFieldsFactory;
        $this->formsFactory = $formsFactory;
        $this->systemStore = $systemStore;

        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {

        $extra = $this->extraFieldsFactory->create()->getCollection();
        $attributes = [
            'email'     => __('Email'),
            'cellphone' => __('Cellphone'),
            'store_id'  => __('Store View'),
            'form_id'   => __('Subscribed from Form'),
            'submitted' => __('Submitted entry in Form'),
        ];

        foreach ($extra as $element) {
            $attributes['field_' . $element->getEntryCode()] = '[' . __('Extra Field') . '] - ' . $element->getName();
        }

        asort($attributes);

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {

        switch ($this->getAttribute()) {
            case 'submitted':
            case 'form_id':
            case 'store_id':
                return 'select';
        }

        $type = $this->extraFieldsFactory->create()
                                         ->getCollection()
                                         ->addFieldToFilter(
                                             'entry_code',
                                             str_replace('field_', '', $this->getAttribute())
                                         )
                                         ->getFirstItem()->getData('type');

        switch ($type) {
            case 'options':
                return 'select';
                break;
        }

        return 'text';
    }

    /**
     * @return mixed
     */
    public function getValueSelectOptions()
    {

        if (stripos($this->getAttribute(), '_') !== false) {
            $field = substr($this->getAttribute(), stripos($this->getAttribute(), '_') + 1);

            $extra = $this->extraFieldsFactory->create()
                                              ->getCollection()
                                              ->addFieldToFilter('entry_code', $field)
                                              ->setPageSize(1)
                                              ->getFirstItem();

            $elements = $this->formElementsFactory->create()
                                                  ->getCollection()->addFieldToFilter(
                    'map',
                    $extra->getData('entry_code')
                );

            $options = [];
            /** @var \Licentia\Forms\Model\FormElements $element */
            foreach ($elements as $element) {
                $options = $options + str_getcsv($element->getOptions());
            }

            if ($options) {
                $this->setData('value_select_options', array_combine($options, $options));
            }
        }

        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'form_id':
                case 'submitted':
                    $extra = $this->formsFactory->create()->getCollection();
                    $options = [];
                    foreach ($extra as $element) {
                        $options[$element->getId()] = $element->getName();
                    }
                    break;
                case 'store_id':
                    $options = $this->systemStore->getStoreValuesForForm();
                    break;
                default:
                    $options = [];
            }
            $this->setData('value_select_options', $options);
        }

        return $this->getData('value_select_options');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $model
     *
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {

        if ($this->getAttribute() == 'submitted') {
            $entries = $this->formEntriesFactory->create()
                                                ->getCollection()
                                                ->addFieldToFilter('subscriber_id', $model->getSubscriberId())
                                                ->addFieldToFilter('form_id', $this->getValue());

            if ($entries->getSize() > 0) {
                $model->setData($this->getAttribute(), $this->getValue());
            } else {
                $model->setData($this->getAttribute(), -1);
            }
        }

        if (stripos($this->getAttribute(), '_') !== false) {
            $field = substr($this->getAttribute(), stripos($this->getAttribute(), '_') + 1);

            $extra = $this->extraFieldsFactory->create()
                                              ->getCollection()
                                              ->addFieldToFilter('entry_code', $field)
                                              ->setPageSize(1)
                                              ->getFirstItem();

            $elements = $this->formElementsFactory->create()
                                                  ->getCollection()
                                                  ->addFieldToFilter('map', $extra->getData('entry_code'))
                                                  ->getFirstItem();

            $options = str_getcsv($elements->getData('options'));

            $model->setData($this->getAttribute(), $options);
        }

        $resultData = $this->registry->registry('panda_segments_data');

        $resultData->setData((string) $this->getAttributeName(), $this->getValue());

        return parent::validate($model);
    }

    /**
     * @return string
     */
    public function getInputType()
    {

        if ($this->getAttribute() == 'email') {
            return 'string';
        }

        if ($this->getAttribute() == 'cellphone') {
            return 'string';
        }

        if ($this->getAttribute() == 'form_id') {
            return 'boolean';
        }

        if ($this->getAttribute() == 'store_id') {
            return 'boolean';
        }

        if ($this->getAttribute() == 'submitted') {
            return 'boolean';
        }

        $type = $this->extraFieldsFactory->create()
                                         ->getCollection()
                                         ->addFieldToFilter(
                                             'entry_code',
                                             str_replace('field_', '', $this->getAttribute())
                                         )
                                         ->getFirstItem()->getData('type');

        switch ($type) {
            case 'options':
                return 'multiselect';
            case 'number':
                return 'numeric';
                break;
        }

        return 'string';
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     *
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {

        $attribute = $this->getAttribute();
        $attributes = $this->getRule()->getCollectedAttributes();
        $attributes[$attribute] = true;
        $this->getRule()->setCollectedAttributes($attributes);

        return $this;
    }
}
