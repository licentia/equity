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
 * Class UpdateSegmentRequest
 *
 * @package Licentia\Equity\Observer
 */
class UpdateSegmentRequest implements ObserverInterface
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $segmentsFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * UpdateSegmentRequest constructor.
     *
     * @param \Licentia\Panda\Helper\Data            $pandaHelper
     * @param \Licentia\Equity\Model\SegmentsFactory $segmentsFactory
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Equity\Model\SegmentsFactory $segmentsFactory
    ) {

        $this->segmentsFactory = $segmentsFactory;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     */
    public function execute(\Magento\Framework\Event\Observer $event)
    {

        try {

            if ($event->getEvent()->getName() == 'sales_order_invoice_register') {

                /** @var  \Magento\Sales\Model\Order $order */
                $order = $event->getEvent()->getOrder();
                $field = $order->getCustomerId() ? $order->getCustomerId() : $order->getCustomerEmail();

                if ($order->getBaseGrandTotal() == $order->getBaseTotalInvoiced()) {
                    $this->segmentsFactory->create()->buildForEvent('order_complete', $field);
                }

                $this->segmentsFactory->create()->buildForEvent('invoice', $field);
            }

            if ($event->getEvent()->getName() == 'checkout_submit_all_after') {

                /** @var  \Magento\Sales\Model\Order $order */
                $order = $event->getEvent()->getOrder();
                $orders = $event->getEvent()->getOrders();

                if ($orders) {
                    foreach ($orders as $order) {
                        $field = $order->getCustomerId() ? $order->getCustomerId() : $order->getCustomerEmail();
                    }
                } else {
                    $field = $order->getCustomerId() ? $order->getCustomerId() : $order->getCustomerEmail();

                }

                $this->segmentsFactory->create()->buildForEvent('order', $field);
            }

            if ($event->getEvent()->getName() == 'customer_register_success') {

                /** @var  \Magento\Customer\Model\Customer $customer */
                $customer = $event->getEvent()->getCustomer();
                $this->segmentsFactory->create()->buildForEvent('customer', $customer->getId());
            }

        } catch (\Exception $e) {
            $this->pandaHelper->logWarning($e);
        }
    }
}
