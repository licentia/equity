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
 * @modified   29/01/20, 15:22 GMT
 *
 */

namespace Licentia\Equity\Controller;

/**
 * Class TwoFactor
 *
 * @package Licentia\Panda\Controller
 */
class TwoFactor extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Licentia\Equity\Model\TwoFactorFactory
     */
    protected $twofactorFactory;

    /**
     *
     */
    public function execute()
    {

        $customerId = $this->customerSession->getId();

        if (!$customerId) {
            $this->customerSession->setData('panda_twofactor_required', false);

            return $this->_redirect('/');
        }

        if ($this->customerSession->getData('panda_twofactor_required') !== true) {
            return $this->_redirect('/');
        }
    }

    /**
     * TwoFactor constructor.
     *
     * @param \Magento\Framework\App\Action\Context               $context
     * @param \Magento\Framework\Data\Form\FormKey\Validator      $formKeyValidator
     * @param \Magento\Customer\Api\CustomerRepositoryInterface   $customerRepository
     * @param \Magento\Framework\Registry                         $coreRegistry
     * @param \Licentia\Equity\Helper\Data                        $pandaHelper
     * @param \Licentia\Equity\Model\TwoFactorFactory             $twoFactorFactory
     * @param \Magento\Customer\Model\Session                     $session
     * @param \Magento\Store\Model\StoreManagerInterface          $storeManagerInterface
     * @param \Magento\Framework\View\Result\PageFactory          $resultPageFactory
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Registry $coreRegistry,
        \Licentia\Equity\Helper\Data $pandaHelper,
        \Licentia\Equity\Model\TwoFactorFactory $twoFactorFactory,
        \Magento\Customer\Model\Session $session,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
    ) {

        parent::__construct($context);

        $this->twofactorFactory = $twoFactorFactory;
        $this->formKeyValidator = $formKeyValidator;
        $this->customerRepository = $customerRepository;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->pandaHelper = $pandaHelper;
        $this->registry = $coreRegistry;
        $this->customerSession = $session;
        $this->storeManager = $storeManagerInterface;
    }
}
