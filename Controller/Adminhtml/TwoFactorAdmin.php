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
 * @modified   03/06/20, 01:43 GMT
 *
 */

namespace Licentia\Equity\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Class TwoFactorAdmin
 *
 * @package Licentia\Equity\Controller\Adminhtml
 */
class TwoFactorAdmin extends \Magento\Backend\App\Action
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
