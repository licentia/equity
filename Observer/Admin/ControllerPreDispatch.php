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
 * @modified   03/06/20, 01:55 GMT
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
     * @var \Licentia\Equity\Logger\Logger
     */
    protected $pandaLogger;

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
     * @param \Magento\Backend\Model\Auth\Session        $userSession
     * @param \Magento\Checkout\Model\Session            $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Licentia\Equity\Helper\Data               $helper
     * @param \Licentia\Equity\Logger\Logger             $pandaLogger
     * @param \Magento\Framework\App\Action\Context      $context
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $userSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Licentia\Equity\Helper\Data $helper,
        \Licentia\Equity\Logger\Logger $pandaLogger,
        \Magento\Framework\App\Action\Context $context
    ) {

        $this->userSession = $userSession;
        $this->redirect = $context->getRedirect();
        $this->pandaLogger = $pandaLogger;
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
            $this->pandaLogger->warning($e->getMessage());
        }
    }
}
