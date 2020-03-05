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
 * @modified   25/02/20, 02:19 GMT
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
        \Licentia\Equity\Block\Plugin\Segments $segments,
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
             !$this->scope->isSetFlag('panda_magna/segments/acl')) ||
            !$customerId) {
            return true;
        }

        $cacheKey = [];
        $cacheKey[] = implode('-', $this->segments->getCustomerSegmentsIds($customerId));

        if ($this->scope->isSetFlag('panda_prices/products/enabled')) {
            $cacheKey[] = sha1($customerGroupId);
        }

        if ($this->scope->isSetFlag('panda_prices/customer/enabled')) {
            $cacheKey[] = 'customer';
            $cacheKey[] = sha1($customerId);
        }

        $result = implode(',', $cacheKey);

        $subject->setValue('CONTEXT_PANDA', $result, '');

        return true;
    }
}
