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
 * @modified   03/06/20, 16:19 GMT
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
