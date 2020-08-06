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

namespace Licentia\Equity\Observer\Admin;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class ControllerPreDispatch
 *
 * @package Licentia\Panda\Observer
 */
class ControllerPreDispatch implements ObserverInterface
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $userSession;

    /**
     * ControllerPreDispatch constructor.
     *
     * @param \Magento\Backend\Model\Auth\Session   $userSession
     * @param \Licentia\Equity\Helper\Data          $helper
     * @param \Licentia\Panda\Helper\Data           $pandaHelper
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $userSession,
        \Licentia\Equity\Helper\Data $helper,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Framework\App\Action\Context $context
    ) {

        $this->userSession = $userSession;
        $this->redirect = $context->getRedirect();
        $this->pandaHelper = $pandaHelper;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     *
     * @return $this|void
     */
    public function execute(\Magento\Framework\Event\Observer $event)
    {

        try {
            /** @var \Magento\Framework\App\Action\Action $controller */
            $controller = $event->getControllerAction();

            /** @var \Magento\Framework\App\RequestInterface $request */
            $request = $event->getEvent()->getRequest();

            if ($this->userSession->getData('panda_twofactor_required') === true) {

                if (($request->getModuleName() == 'admin' &&
                     $request->getControllerName() == 'auth' &&
                     $request->getActionName() == 'logout') ||
                    $request->isAjax()
                ) {

                } else {

                    if ($request->getModuleName() != 'pandae' &&
                        $request->getControllerName() != 'twofactoradmin' &&
                        $this->userSession->getData('panda_twofactor_required') === true
                    ) {

                        $controller->getActionFlag()
                                   ->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);

                        $this->redirect->redirect($controller->getResponse(), 'pandae/twofactoradmin/auth');

                        return $this;
                    }
                }
            }
        } catch (\Exception $e) {
            $this->pandaHelper->logWarning($e);
        }
    }
}
