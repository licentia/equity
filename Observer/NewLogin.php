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
 * Class NewLogin
 *
 * @package Licentia\Equity\Observer
 */
class NewLogin implements ObserverInterface
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Licentia\Equity\Model\TwoFactorFactory
     */
    protected $twoFactorFactory;

    /**
     * NewLogin constructor.
     *
     * @param \Licentia\Panda\Helper\Data             $pandaHelper
     * @param \Licentia\Equity\Model\TwoFactorFactory $twoFactorFactory
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Equity\Model\TwoFactorFactory $twoFactorFactory
    ) {

        $this->twoFactorFactory = $twoFactorFactory;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     */
    public function execute(\Magento\Framework\Event\Observer $event)
    {

        try {
            $this->twoFactorFactory->create()->checkLogin($event);
        } catch (\Exception $e) {
            $this->pandaHelper->logWarning($e);
        }
    }
}
