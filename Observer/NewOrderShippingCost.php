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

namespace Licentia\Equity\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class NewOrder
 *
 * @package Licentia\Panda\Observer
 */
class NewOrderShippingCost implements ObserverInterface
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * NewOrder constructor.
     *
     * @param \Licentia\Panda\Helper\Data     $pandaHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {

        $this->checkoutSession = $checkoutSession;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        try {
            /** @var  \Magento\Sales\Model\Order $order */
            $order = $observer->getEvent()->getOrder();
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
    }
}
