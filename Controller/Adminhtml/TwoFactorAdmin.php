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

namespace Licentia\Equity\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Class TwoFactorAdmin
 *
 * @package Licentia\Equity\Controller\Adminhtml
 */
class TwoFactorAdmin extends Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PublicCookieMetadata
     */
    protected $cookieMetadataFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $userSession;

    /**
     * @var \Licentia\Equity\Model\TwoFactorAdminFactory
     */
    protected $twofactorFactory;

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected $pandaHelper;

    /**
     * TwoFactorAdmin constructor.
     *
     * @param \Licentia\Equity\Helper\Data                          $helperData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface    $scopeConfigInterface
     * @param \Magento\Framework\Stdlib\Cookie\PublicCookieMetadata $cookieMetadata
     * @param \Magento\Framework\Stdlib\CookieManagerInterface      $cookieManager
     * @param \Magento\Backend\Model\Auth\Session                   $userSession
     * @param \Licentia\Equity\Model\TwoFactorAdminFactory          $twoFactorFactory
     * @param Action\Context                                        $context
     * @param \Magento\Framework\View\Result\PageFactory            $resultPageFactory
     */
    public function __construct(
        \Licentia\Equity\Helper\Data $helperData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Framework\Stdlib\Cookie\PublicCookieMetadata $cookieMetadata,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Backend\Model\Auth\Session $userSession,
        \Licentia\Equity\Model\TwoFactorAdminFactory $twoFactorFactory,
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {

        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;

        $this->pandaHelper = $helperData;
        $this->twofactorFactory = $twoFactorFactory;
        $this->userSession = $userSession;
        $this->scopeConfig = $scopeConfigInterface;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadata;
    }

    protected function _isAllowed()
    {

        return true;
    }

    /**
     *
     */
    public function execute()
    {
    }

}
