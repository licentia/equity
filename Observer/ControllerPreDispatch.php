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
 * @modified   03/06/20, 17:10 GMT
 *
 */

namespace Licentia\Equity\Observer;

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
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Licentia\Panda\Model\AutorespondersFactory
     */
    protected $autorespondersFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    /**
     * ControllerPreDispatch constructor.
     *
     * @param \Magento\Customer\Model\Session            $customerSession
     * @param \Magento\Checkout\Model\Session            $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Licentia\Equity\Helper\Data               $helper
     * @param \Licentia\Panda\Helper\Data                $pandaHelper
     * @param \Magento\Framework\App\Action\Context      $context
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Licentia\Equity\Helper\Data $helper,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Framework\App\Action\Context $context
    ) {

        $this->redirect = $context->getRedirect();
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->pandaHelper = $pandaHelper;
        $this->helper = $helper;
        $this->storeManager = $storeManager;
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

            if ($this->customerSession->getData('panda_twofactor_required') === true) {
                if ($request->getModuleName() == 'customer' &&
                    $request->getControllerName() == 'account' &&
                    $request->getActionName() == 'logout'

                ) {
                } else {

                    if ($request->getModuleName() != 'pandae' &&
                        $request->getControllerName() != 'twofactor' &&
                        $this->customerSession->getData('panda_twofactor_required') === true
                    ) {

                        $controller->getActionFlag()
                                   ->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);

                        $this->redirect->redirect($controller->getResponse(), 'pandae/twofactor/auth');

                        return $this;
                    }
                }
            }
        } catch (\Exception $e) {
            $this->pandaHelper->logWarning($e);
        }
    }
}
