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
 * Class UpdateSalesExtraCosts
 *
 * @package Licentia\Panda\Observer
 */
class UpdateSalesExtraCosts implements ObserverInterface
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected \Licentia\Panda\Helper\Data $pandaHelper;

    /**
     * @var \Licentia\Equity\Model\Sales\ExtraCostsFactory
     */
    protected \Licentia\Equity\Model\Sales\ExtraCostsFactory $extraCostsFactory;

    /**
     * ConvertOrder constructor.
     *
     * @param \Licentia\Panda\Helper\Data                    $pandaHelper
     * @param \Licentia\Equity\Model\Sales\ExtraCostsFactory $extraCostsFactory
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Equity\Model\Sales\ExtraCostsFactory $extraCostsFactory
    ) {

        $this->pandaHelper = $pandaHelper;
        $this->extraCostsFactory = $extraCostsFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     */
    public function execute(\Magento\Framework\Event\Observer $event)
    {

        try {

            /** @var \Magento\Sales\Model\Order $order */
            $order = $event->getEvent()->getOrder();

            if (($order->getState() == 'complete' || $order->getState() == 'closed') &&
                !$order->getData('panda_extra_costs')) {
                $this->extraCostsFactory->create()->updateOrdersOtherCosts($order);
            }
        } catch (\Exception $e) {
            $this->pandaHelper->logWarning($e);
        }
    }
}
