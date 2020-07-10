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
 * Class UpdateUserPhone
 *
 * @package Licentia\Equity\Observer\Admin
 */
class UpdateUserPhone implements ObserverInterface
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $userSession;

    /**
     * UpdateUserPhone constructor.
     *
     * @param \Magento\Backend\Model\Auth\Session   $userSession
     * @param \Licentia\Equity\Helper\Data          $pandaHelper
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $userSession,
        \Licentia\Equity\Helper\Data $pandaHelper,
        \Magento\Framework\App\Action\Context $context
    ) {

        $this->userSession = $userSession;
        $this->redirect = $context->getRedirect();
        $this->pandaHelper = $pandaHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     *
     * @return $this|void
     */
    public function execute(\Magento\Framework\Event\Observer $event)
    {

        try {

            /** @var \Magento\Framework\App\RequestInterface $request */
            $request = $event->getEvent()->getRequest();

            $this->userSession->getUser()
                              ->setData('panda_twofactor_number', $request->getParam('panda_twofactor_number'))
                              ->save();

        } catch (\Exception $e) {
            $this->pandaHelper->logWarning($e);
        }
    }
}
