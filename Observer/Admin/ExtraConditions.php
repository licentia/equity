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
