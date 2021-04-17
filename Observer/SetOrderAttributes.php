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

namespace Licentia\Equity\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class SetOrderAttributes
 *
 * @package Licentia\Panda\Observer
 */
class SetOrderAttributes implements ObserverInterface
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Licentia\Panda\Model\Session
     */
    protected $pandaSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * SetOrderAttributes constructor.
     *
     * @param \Licentia\Panda\Model\Session   $pandaSession
     * @param \Licentia\Panda\Helper\Data     $pandaHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Licentia\Panda\Model\Session $pandaSession,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {

        $this->checkoutSession = $checkoutSession;
        $this->pandaSession = $pandaSession;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     *
     * @return bool
     */
    public function execute(\Magento\Framework\Event\Observer $event)
    {

        /* @var $order \Magento\Sales\Model\Order */
        $order = $event->getOrder();
        try {
            $order->setData('panda_acquisition_campaign', $this->pandaSession->getData('panda_acquisition_campaign'));

            $rate = $this->checkoutSession->getQuote()->getShippingAddress()->getShippingRatesCollection();
            $cost = 0;
            foreach ($rate as $item) {
                if ($item->getCode() == $order->getShippingMethod()) {
                    $cost = $item->getCost();
                    break;
                }
            }

            $order->setData('panda_shipping_cost', $cost);
        } catch (\Exception $e) {
            $this->pandaHelper->logWarning($e);
        }

        return false;
    }
}
