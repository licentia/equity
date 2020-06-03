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
 * @modified   02/06/20, 22:35 GMT
 *
 */

namespace Licentia\Equity\Block\Adminhtml;

use Magento\Framework\View\Element\Template;

/**
 * Class TwoFactorAdmin
 *
 * @package Licentia\Equity\Block
 */
class TwoFactorAdmin extends \Magento\Backend\Block\Template
{

    /**
     * @var \Magento\Theme\Block\Html\Header\Logo
     */
    protected $logo;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Licentia\Equity\Model\TwoFactorAdminFactory
     */
    protected $twofactorFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $userSession;

    /**
     * TwoFactorAdmin constructor.
     *
     * @param \Magento\Backend\Model\Auth\Session          $userSession
     * @param \Magento\Theme\Block\Html\Header\Logo        $logo
     * @param \Licentia\Equity\Model\TwoFactorAdminFactory $twoFactorFactory
     * @param \Magento\Framework\Registry                  $registry
     * @param \Magento\Backend\Block\Template\Context      $context
     * @param array                                        $data
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $userSession,
        \Magento\Theme\Block\Html\Header\Logo $logo,
        \Licentia\Equity\Model\TwoFactorAdminFactory $twoFactorFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {

        parent::__construct($context, $data);

        $this->logo = $logo;
        $this->twofactorFactory = $twoFactorFactory;
        $this->userSession = $userSession;
        $this->registry = $registry;
    }

    /**
     * @return bool
     */
    public function getAllowRemember()
    {

        return $this->_scopeConfig->isSetFlag('panda_customer/twofactor_admin/allow_remember');
    }

    /**
     * @return mixed
     */
    public function getDaysToRemember()
    {

        return $this->_scopeConfig->getValue('panda_customer/twofactor_admin/remember_days');
    }

    /**
     * @return bool|int
     */
    public function canGenerateCode()
    {

        return $this->twofactorFactory->create()
                                      ->canGenerateCode($this->userSession->getUser());
    }

    /**
     * @return mixed
     */
    public function hasCellphone()
    {

        return $this->userSession->getUser()->getData('panda_twofactor_number');
    }

    /**
     * Get logo image URL
     *
     * @return string
     */
    public function getLogoSrc()
    {

        return $this->logo->getLogoSrc();
    }

    /**
     * Get logo text
     *
     * @return string
     */
    public function getLogoAlt()
    {

        return $this->logo->getLogoAlt();
    }

    /**
     * Get logo width
     *
     * @return int
     */
    public function getLogoWidth()
    {

        return $this->logo->getLogoWidth();
    }

    /**
     * Get logo height
     *
     * @return int
     */
    public function getLogoHeight()
    {

        return $this->logo->getLogoHeight();
    }
}
