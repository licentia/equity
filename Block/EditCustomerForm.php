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
     * @param \Licentia\Equity\Model\TwoFactorFactory $twoFactorFactory
     * @param \Licentia\Equity\Helper\Data            $pandaHelper
     * @param \Magento\Customer\Model\Session         $session
     * @param Template\Context                        $context
     * @param array                                   $data
     */
    public function __construct(
        \Licentia\Equity\Model\TwoFactorFactory $twoFactorFactory,
        \Licentia\Equity\Helper\Data $pandaHelper,
        \Magento\Customer\Model\Session $session,
        Template\Context $context,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->pandaHelper = $pandaHelper;
        $this->customerSession = $session;
        $this->twofactorFactory = $twoFactorFactory;
    }

    public function getCustomer()
    {

        return $this->customerSession->getCustomer();
    }

    public function isTwoFactorEnabled()
    {

        return $this->twofactorFactory->create()->isTwoFactorEnabledForCustomer();
    }

    public function isTwoFactorOptional()
    {

        return $this->twofactorFactory->create()->isTwoFactorOptionalForCustomer();

    }

}
