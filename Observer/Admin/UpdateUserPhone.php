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
 * @modified   03/06/20, 16:52 GMT
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
     * @param \Magento\Backend\Model\Auth\Session        $userSession
     * @param \Magento\Checkout\Model\Session            $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Licentia\Equity\Helper\Data               $pandaHelper
     * @param \Magento\Framework\App\Action\Context      $context
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $userSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
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
