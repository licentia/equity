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

namespace Licentia\Equity\Block;

use Magento\Framework\View\Element\Template;

/**
 * Class TwoFactor
 *
 * @package Licentia\Panda\Block
 */
class TwoFactor extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Theme\Block\Html\Header\Logo
     */
    protected $logo;

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
     * TwoFactor constructor.
     *
     * @param \Magento\Theme\Block\Html\Header\Logo   $logo
     * @param \Licentia\Equity\Model\TwoFactorFactory $twoFactorFactory
     * @param \Magento\Customer\Model\Session         $session
     * @param \Magento\Framework\Registry             $registry
     * @param Template\Context                        $context
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Theme\Block\Html\Header\Logo $logo,
        \Licentia\Equity\Model\TwoFactorFactory $twoFactorFactory,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\Registry $registry,
        Template\Context $context,
        array $data = []
    ) {

        parent::__construct($context, $data);

        $this->logo = $logo;
        $this->twofactorFactory = $twoFactorFactory;

        $this->customerSession = $session;
        $this->registry = $registry;
    }

    /**
     * @return bool|int
     */
    public function canGenerateCode()
    {

        return $this->twofactorFactory->create()
                                      ->canGenerateCode($this->customerSession->getCustomer());
    }

    /**
     * @return mixed
     */
    public function hasCellphone()
    {

        return $this->customerSession->getCustomer()->getData('panda_twofactor_number');
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
