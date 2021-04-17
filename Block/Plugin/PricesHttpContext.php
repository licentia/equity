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

namespace Licentia\Equity\Block\Plugin;

/**
 * Class HttpContext
 *
 * @package Licentia\Panda\Block\Plugin
 */
class PricesHttpContext
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected \Magento\Customer\Model\Session $customerSession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected \Magento\Framework\App\Config\ScopeConfigInterface $scope;

    /**
     * @var Segments
     */
    protected Segments $segments;

    /**
     * PageCache constructor.
     *
     * @param Segments                                           $segments
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Magento\Customer\Model\Session                    $customerSession
     */
    public function __construct(
        Segments $segments,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Customer\Model\Session $customerSession
    ) {

        $this->customerSession = $customerSession;
        $this->scope = $scopeConfigInterface;
        $this->segments = $segments;
    }

    /**
     * @param \Magento\Framework\App\Http\Context $subject
     *
     * @return bool
     */
    public function beforeGetVaryString(\Magento\Framework\App\Http\Context $subject)
    {

        $customerId = $this->customerSession->getId();
        $customerGroupId = $this->customerSession->getCustomerGroupId();

        if (!$this->scope->isSetFlag('panda_prices/segments/enabled') &&
            !$this->scope->isSetFlag('panda_magna/segments/acl') &&
            !$this->scope->isSetFlag('panda_magna/products/enabled') &&
            !$this->scope->isSetFlag('panda_prices/import/enabled') &&
            !$this->scope->isSetFlag('panda_prices/products/enabled') &&
            !$this->scope->isSetFlag('panda_prices/customers/enabled')) {
            return true;
        }

        $cacheKey = [];

        if ($this->scope->isSetFlag('panda_prices/segments/enabled') ||
            $this->scope->isSetFlag('panda_magna/segments/acl') ||
            $this->scope->isSetFlag('panda_magna/products/enabled')) {
            $cacheKey[] = implode('-', $this->segments->getCustomerSegmentsIds($customerId));
        }

        if ($this->scope->isSetFlag('panda_prices/products/enabled')) {
            $cacheKey[] = sha1($customerGroupId);
        }

        if ($this->scope->isSetFlag('panda_prices/customers/enabled') ||
            $this->scope->isSetFlag('panda_prices/import/enabled')) {
            $cacheKey[] = sha1($customerId);
        }

        $result = implode(',', $cacheKey);

        $subject->setValue('CONTEXT_PANDA', $result, '');

        return true;
    }
}
