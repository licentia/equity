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

namespace Licentia\Equity\Model\Rule\Condition;

use Magento\Rule\Model\Condition\Context;

/**
 * Class Segment
 *
 * @package Licentia\Equity\Model\Rule\Condition
 */
class Segment extends \Magento\Rule\Model\Condition\AbstractCondition
{

    /**
     * @var \Licentia\Equity\Model\SegmentsFactory
     */
    protected \Licentia\Equity\Model\SegmentsFactory $segmentsFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected \Licentia\Panda\Helper\Data $pandaHelper;

    /**
     * @param \Licentia\Panda\Helper\Data            $pandaHelper
     * @param \Licentia\Equity\Model\SegmentsFactory $segmentsFactory
     * @param Context                                $context
     * @param array                                  $data
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Equity\Model\SegmentsFactory $segmentsFactory,
        Context $context,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->segmentsFactory = $segmentsFactory;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     * @return string
     */
    public function getInputType()
    {

        return 'select';
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {

        return 'select';
    }

    /**
     * @return string
     */
    public function getAttributeName()
    {

        return 'Customer Segment';
    }

    /**
     * @return $this
     */
    public function getAttributeElement()
    {

        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {

        $attributes = [
            'customer_segment' => __('Customer Segment'),
        ];

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValueSelectOptions()
    {

        if (!$this->hasData('value_select_options')) {
            $options = $this->segmentsFactory->create()->getOptionArray(false);
            $this->setData('value_select_options', $options);
        }

        return $this->getData('value_select_options');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $object)
    {

        $object->setData(
            $this->getAttribute(),
            $this->pandaHelper->isCustomerInSegment($this->getValueParsed())
        );

        return parent::validate($object);
    }
}
