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

namespace Licentia\Equity\Block;

use Magento\Framework\View\Element\Template;

/**
 * Class TwoFactor
 *
 * @package Licentia\Panda\Block
 */
class TwoFactor extends Template
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
     * @return bool
     */
    public function getAllowRemember()
    {

        return $this->_scopeConfig->isSetFlag('panda_customer/twofactor/allow_remember');
    }

    /**
     * @return mixed
     */
    public function getDaysToRemember()
    {

        return $this->_scopeConfig->getValue('panda_customer/twofactor/remember_days');
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
