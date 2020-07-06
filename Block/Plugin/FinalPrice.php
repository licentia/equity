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

use Magento\Customer\Model\Session;

/**
 * Class FinalPrice
 *
 * @package Licentia\Panda\Block\Plugin
 */
class FinalPrice
{

    /**
     * @var Session
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
     * @param null                           $special
     *
     * @return bool|float|mixed|string|null
     */
    protected function getProductPrice(\Magento\Catalog\Model\Product $product, $result, $special = null)
    {

        \Magento\Framework\Profiler::start('PANDA GET PRODUCT PRICE: ' . $product->getSku());

        $minPrice = null;
        $maxPrice = null;
        $price = null;
        $products = null;
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
                if (is_array($products) && $product->getData('panda_price_expression')) {
                    $products['price'] = $product->getData('panda_price_expression');
                }

            }

            if ($this->scope->isSetFlag('panda_prices/customers/enabled')) {
                $products = $this->scope->getValue(
                    'panda_prices/customers',
                    \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
                );
                if (is_array($products) && $this->customerSession->getCustomer()->getData('panda_price_expression')) {
                    $products['price'] = $this->customerSession->getCustomer()->getData('panda_price_expression');
                }
            }

            if (is_array($products)) {

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

                if ($price >= 0) {
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

                    $decimals = $price - floor($price);

                    if (isset($products['round_up_99'], $products['round_up_99_min'], $products['round_up_99_decimals']) &&
                        $price >= $products['round_up_99_min']
                        && $decimals > floatval('0.' . $products['round_up_99_decimals'])
                    ) {
                        if ($products['round_up_99'] == 1) {
                            $price = floor($price) . '.99';
                        }
                    }

                    if (isset($products['round_up_49'], $products['round_up_49_min'], $products['round_up_49_decimals']) &&
                        $price >= $products['round_up_49_min'] &&
                        $decimals > floatval('0.' . $products['round_up_49_decimals'])
                    ) {
                        if ($products['round_up_49'] == 1) {
                            $price = floor($price) . '.49';
                        }
                    }

                    if (isset($products['round_down_99']) && $products['round_down_99'] == 1 && $decimals == 0) {
                        $price = $price - 0.01;
                    }
                }

                if ($price) {
                    if ($minPrice) {
                        $price = max($price, $minPrice);
                    }
                    if ($maxPrice) {
                        $price = min($price, $maxPrice);
                    }

                    $result = $price;
                }
            }
        }

        if ($this->scope->isSetFlag('panda_prices/segments/enabled')) {
            $result = $this->pandaHelper->getSegmentPrice($customerId, $product, $result);
        }

        if ($this->scope->isSetFlag('panda_prices/import/enabled')) {
            $result = $this->pandaHelper->getCustomerPrice($customerId, $product, $result);
        }

        \Magento\Framework\Profiler::stop();

        if ($special && $special < $result) {
            $result = $special;
        }

        return (float) $result;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param                                $result
     *
     * @return bool
     */
    public function afterGetSpecialPrice(\Magento\Catalog\Model\Product $product, $result)
    {

        if ($this->scope->getValue('panda_prices/display/prices') == 'special_price') {
            return $this->getProductPrice($product, $product->getPrice(), $result);
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

        if ($this->scope->getValue('panda_prices/display/prices') == 'price') {
            return $this->getProductPrice($product, $result);
        }

        return $result;
    }
}
