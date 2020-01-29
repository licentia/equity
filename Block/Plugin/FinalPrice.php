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

namespace Licentia\Equity\Block\Plugin;

use Magento\Customer\Model\Session;

/**
 * Class FinalPrice
 *
 * @package Licentia\Panda\Block\Plugin
 */
class FinalPrice
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scope;

    /**
     * @var \Licentia\Panda\Helper\Math
     */
    protected $mathHelper;

    /**
     * PriceBoxTags constructor.
     *
     * @param \Licentia\Equity\Helper\Data                       $pandaHelper
     * @param \Licentia\Equity\Helper\Math                       $mathHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param Session                                            $customerSession
     */
    public function __construct(
        \Licentia\Equity\Helper\Data $pandaHelper,
        \Licentia\Equity\Helper\Math $mathHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        Session $customerSession
    ) {

        $this->pandaHelper = $pandaHelper;
        $this->mathHelper = $mathHelper;
        $this->customerSession = $customerSession;
        $this->scope = $scopeConfigInterface;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param                                $result
     *
     * @return mixed
     */
    public function afterGetCost(\Magento\Catalog\Model\Product $product, $result)
    {

        if ($result == 0) {
            $cost = (int) $this->scope->getValue(
                'panda_equity/products/cost',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            );

            if ($cost > 0) {
                $result = round($product->getPrice() * $cost / 100, 4);
            }
        }

        return $result;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param                                $result
     *
     * @return bool
     */
    public function afterGetPrice(\Magento\Catalog\Model\Product $product, $result)
    {

        \Magento\Framework\Profiler::start('PANDA GET PRODUCT PRICE: ' . $product->getSku());

        $minPrice = null;
        $maxPrice = null;
        $price = null;
        #$customerId = isset($_SESSION['customer_base']['customer_id']) ? $_SESSION['customer_base']['customer_id'] : null;
        #$customerGroupId = isset($_SESSION['customer_base']['customer_group_id']) ? $_SESSION['customer_base']['customer_group_id'] : null;

        $customerId = $this->customerSession->getId();
        $customerGroupId = $this->customerSession->getCustomerGroupId();

        if ((bool) $product->getData('panda_prices_disabled') == false ||
            ($this->customerSession->getCustomerId() &&
             (bool) $this->customerSession->getCustomer()->getData('panda_prices_disabled') == false)
        ) {
            if ($this->scope->isSetFlag('panda_prices/products/enabled')) {
                $products = $this->scope->getValue(
                    'panda_prices/products',
                    \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
                );

                $customerGroups = explode(',', $products['customer_groups']);
                $customerGroups = array_filter($customerGroups);

                if ($customerGroups && !in_array($customerGroupId, $customerGroups)) {
                    return $result;
                }

                $price = $this->mathHelper->getEvaluatedProductPriceExpression(
                    $product,
                    $this->customerSession,
                    $products['price'],
                    'global'
                );

                if ($price) {
                    $minPrice = $this->mathHelper->getEvaluatedProductPriceExpression(
                        $product,
                        $this->customerSession,
                        $products['min_price'],
                        'global'
                    );

                    $maxPrice = $this->mathHelper->getEvaluatedProductPriceExpression(
                        $product,
                        $this->customerSession,
                        $products['max_price'],
                        'global'
                    );

                    if ($price && $minPrice) {
                        $price = max($price, $minPrice);
                    }
                    if ($price && $maxPrice) {
                        $price = min($price, $maxPrice);
                    }

                    $decimals = $price - floor($price);

                    if (isset($products['round_up_99'], $products['round_up_99_min'], $products['round_up_99_decimals']) &&
                        $price >= $products['round_up_99_min']
                        && $decimals > floatval('0.' . $products['round_up_99_decimals'])
                    ) {
                        $price = floor($price) . '.99';
                    }

                    if (isset($products['round_up_49'], $products['round_up_49_min'], $products['round_up_49_decimals']) &&
                        $price >= $products['round_up_49_min'] &&
                        $decimals > floatval('0.' . $products['round_up_49_decimals'])
                    ) {
                        $price = floor($price) . '.49';
                    }

                    if (isset($products['round_down_99']) && $decimals == 0) {
                        $price = $price - 0.01;
                    }
                }
            }

            if ($price) {
                $result = $price;
            }
        }

        if ($this->scope->isSetFlag('panda_magna/prices/enabled')) {
            $result = $this->pandaHelper->getSegmentPrice($customerId, $product, $result);
        }
        \Magento\Framework\Profiler::stop();

        return (float) $result;
    }
}
