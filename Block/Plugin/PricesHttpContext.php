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
    protected $customerSession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scope;

    /**
     * @var Segments
     */
    protected $segments;

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

        if ((!$this->scope->isSetFlag('panda_magna/prices/enabled') &&
             !$this->scope->isSetFlag('panda_prices/products/enabled') &&
             !$this->scope->isSetFlag('panda_prices/customers/enabled') &&
             !$this->scope->isSetFlag('panda_magna/segments/acl'))) {
            return true;
        }

        $cacheKey = [];
        $cacheKey[] = implode('-', $this->segments->getCustomerSegmentsIds($customerId));

        if ($this->scope->isSetFlag('panda_prices/products/enabled')) {
            $cacheKey[] = sha1($customerGroupId);
        }

        if ($this->scope->isSetFlag('panda_prices/customers/enabled')) {
            $cacheKey[] = 'customer';
            $cacheKey[] = sha1($customerId);
        }

        $result = implode(',', $cacheKey);

        $subject->setValue('CONTEXT_PANDA', $result, '');

        return true;
    }
}
