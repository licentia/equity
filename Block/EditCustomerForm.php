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
 * @modified   03/06/20, 19:30 GMT
 *
 */

namespace Licentia\Equity\Block;

use Magento\Framework\View\Element\Template;

/**
 * Class TwoFactor
 *
 * @package Licentia\Panda\Block
 */
class EditCustomerForm extends Template
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Licentia\Equity\Model\TwoFactorFactory
     */
    protected $twofactorFactory;

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected $pandaHelper;

    /**
     * EditCustomerForm constructor.
     *
     * @param \Licentia\Equity\Helper\Data    $pandaHelper
     * @param \Magento\Customer\Model\Session $session
     * @param Template\Context                $context
     * @param array                           $data
     */
    public function __construct(
        \Licentia\Equity\Helper\Data $pandaHelper,
        \Magento\Customer\Model\Session $session,
        Template\Context $context,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->pandaHelper = $pandaHelper;

        $this->customerSession = $session;
    }

    public function getCustomer()
    {

        return $this->customerSession->getCustomer();
    }

    public function isTwoFactorEnabled()
    {

        $customer = $this->customerSession->getCustomer();
        $data = $this->_scopeConfig->getValue(
            'panda_customer/twofactor',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $groups = explode(',', $data['customer_groups']);
        $segments = explode(',', $data['segments']);

        if (in_array($customer->getGroupId(), $groups) ||
            array_intersect($segments, $this->pandaHelper->getCustomerSegmentsIds())) {
            return false;
        }

        return false;
    }

}
