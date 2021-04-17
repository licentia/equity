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
    protected \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    protected \Magento\Framework\Registry $registry;

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected \Licentia\Equity\Helper\Data $pandaHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected \Magento\Customer\Model\Session $customerSession;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected \Magento\Framework\View\Result\PageFactory $resultPageFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected \Magento\Store\Model\StoreManagerInterface $storeManager;

    /**
     * @var \Licentia\Equity\Model\TwoFactorFactory
     */
    protected \Licentia\Equity\Model\TwoFactorFactory $twofactorFactory;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PublicCookieMetadata
     */
    protected \Magento\Framework\Stdlib\Cookie\PublicCookieMetadata $cookieMetadataFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    /**
     * TwoFactor constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface    $scopeConfigInterface
     * @param \Magento\Framework\Stdlib\Cookie\PublicCookieMetadata $cookieMetadata
     * @param \Magento\Framework\Stdlib\CookieManagerInterface      $cookieManager
     * @param \Magento\Framework\App\Action\Context                 $context
     * @param \Magento\Framework\Data\Form\FormKey\Validator        $formKeyValidator
     * @param \Magento\Customer\Api\CustomerRepositoryInterface     $customerRepository
     * @param \Magento\Framework\Registry                           $coreRegistry
     * @param \Licentia\Equity\Helper\Data                          $pandaHelper
     * @param \Licentia\Equity\Model\TwoFactorFactory               $twoFactorFactory
     * @param \Magento\Customer\Model\Session                       $session
     * @param \Magento\Store\Model\StoreManagerInterface            $storeManagerInterface
     * @param \Magento\Framework\View\Result\PageFactory            $resultPageFactory
     * @param \Magento\Framework\Controller\Result\ForwardFactory   $resultForwardFactory
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Framework\Stdlib\Cookie\PublicCookieMetadata $cookieMetadata,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
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

        $this->scopeConfig = $scopeConfigInterface;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadata;
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
}
