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
 * @modified   24/03/20, 18:42 GMT
 *
 */

namespace Licentia\Equity\Helper;

use Magento\Framework\App\Helper\Context;

/**
 * Class Math
 *
 * @package Licentia\Panda\Helper
 */
class Math extends \Magento\Framework\App\Helper\AbstractHelper
{

    const CONSTRUCTION_IF_PATTERN = '/{{if\s*(.*?)}}(.*?)({{else}}(.*?))?{{\\/if\s*}}/si';

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $cacheManager;

    /**
     * @var bool
     */
    protected $evaluate = false;

    /**
     * @var \Licentia\Equity\Model\KpisFactory
     */
    protected $kpisFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFactory
     */
    protected $dateFactory;

    /**
     * Math constructor.
     *
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory
     * @param \Licentia\Equity\Model\KpisFactory                 $kpisFactory
     * @param \Magento\Framework\App\CacheInterface              $cache
     * @param Context                                            $context
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory,
        \Licentia\Equity\Model\KpisFactory $kpisFactory,
        \Magento\Framework\App\CacheInterface $cache,
        Context $context
    ) {

        parent::__construct($context);

        $this->dateFactory = $dateFactory;
        $this->kpisFactory = $kpisFactory;
        $this->cacheManager = $cache;
    }

    /**
     * @param        $sku
     * @param string $type
     *
     * @return \Magento\Framework\DataObject
     */
    public function getBestSeller($sku, $type = 'daily')
    {

        $resource = $this->kpisFactory->create()->getResource();

        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
        $connection = $resource->getConnection();

        if ($type == 'daily') {
            $format = '%Y-%m-%d';
        } elseif ($type == 'monthly') {
            $format = '%Y-%m';
        } elseif ($type == 'yearly') {
            $format = '%Y';
        } else {
            return new \Magento\Framework\DataObject();
        }

        $select = $connection->select()
                             ->from(
                                 $resource->getTable('sales_invoice_item'),
                                 [
                                     'qty_invoiced' => new \Zend_Db_Expr('SUM(total_qty)'),
                                     'min_price'    => new \Zend_Db_Expr('MIN(base_price * base_to_global_rate )'),
                                     'avg_price'    => new \Zend_Db_Expr('AVG(base_price * base_to_global_rate )'),
                                     'max_price'    => new \Zend_Db_Expr('MAX(base_price * base_to_global_rate )'),
                                 ]
                             )
                             ->join(
                                 $resource->getTable('sales_invoice'),
                                 $resource->getTable('sales_invoice') . '.entity_id=' . $resource->getTable(
                                     'sales_invoice_item'
                                 ) . '.parent_id',
                                 []
                             )
                             ->where('sku=?', $sku)
                             ->where(
                                 "DATE_FORMAT({$resource->getTable('sales_invoice')}.created_at , '$format') = ?",
                                 $this->dateFactory->create()->gmtDate()
                             )
                             ->limit(1);

        $row = $connection->fetchRow($select);

        $object = new \Magento\Framework\DataObject();
        $object->setData($row);

        return $object;
    }

    /**
     * @param $expression
     *
     * @return bool|null
     */
    public function evaluateIfExpression($expression)
    {

        $expression = str_replace(' ', '', $expression);
        $parts = preg_split('/(<|<=|=|>|>=)/', $expression, 0, PREG_SPLIT_DELIM_CAPTURE);

        if (count($parts) != 3) {
            return null;
        }

        switch ($parts[1]) {
            CASE '>':
                return $parts[0] > $parts[2] ? true : false;
                break;
            CASE '>=':
                return $parts[0] >= $parts[2] ? true : false;
                break;
            CASE '=':
                return $parts[0] == $parts[2] ? true : false;
                break;
            CASE '<':
                return $parts[0] < $parts[2] ? true : false;
                break;
            CASE '<=':
                return $parts[0] <= $parts[2] ? true : false;
                break;
            default:
                return null;
        }

        return null;
    }

    /**
     * @param \Magento\Catalog\Model\Product  $product
     * @param \Magento\Customer\Model\Session $customerSession
     * @param                                 $expression
     *
     * @param string                          $scope
     *
     * @return null
     */
    public function getEvaluatedProductPriceExpression(
        \Magento\Catalog\Model\Product $product,
        \Magento\Customer\Model\Session $customerSession,
        $expression,
        $scope = 'global'
    ) {

        $expression = preg_replace('/[\x00-\x1F\x7F]/u', '', $expression);
        $cacheKey = 'panda_' . $scope . sha1('_prices_product_' . $expression . '_' . $product->getId());

        if ($scope == 'global' && $this->cacheManager->getFrontend()->test($cacheKey)) {
            #return $this->cacheManager->getFrontend()->load($cacheKey);
        }

        if ($scope == 'customer') {
            $this->loadPandaEquity($customerSession);
        }

        if ($customerSession->getData($cacheKey) && $scope == 'customer') {
            return $customerSession->getData($cacheKey);
        }

        if ($product->getData('panda_price_expression')) {
            $expression = $product->getData('panda_price_expression');
        }

        if ($customerSession->getCustomer()->getData('panda_price_expression')) {
            $expression = $customerSession->getCustomer()->getData('panda_price_expression');
        }
        if (stripos($expression, '{{if') !== false) {
            preg_match_all(self::CONSTRUCTION_IF_PATTERN, $expression, $constructions, PREG_SET_ORDER);

            foreach ($constructions as $eval) {
                $ifCondition = $eval[1];

                $ifCondition = $this->replaceExpressionIf($product, $ifCondition, 'p');
                $ifCondition = $this->replaceExpressionIf($customerSession->getCustomer(), $ifCondition, 'c');
                $ifCondition = $this->replaceExpressionIf(
                    $customerSession->getCustomerPandaEquity(),
                    $ifCondition,
                    'e'
                );

                if (preg_match('/[a-z]/si', $ifCondition)) {
                    $expression = $this->replaceInExpression($eval[0], '', $expression);
                } else {
                    $ifCondition = preg_replace('/[^0-9()&><=.]/si', '', $ifCondition);

                    $ifCondition = $this->evaluateIfExpression($ifCondition);

                    if ($ifCondition === null) {
                        return null;
                    }

                    if (!$ifCondition && isset($eval[4])) {
                        $expression = $this->replaceInExpression($eval[0], $eval[4], $expression);
                    } elseif ($ifCondition) {
                        $expression = $this->replaceInExpression($eval[0], $eval[2], $expression);
                    } elseif (!$ifCondition) {
                        $expression = $this->replaceInExpression($eval[0], '', $expression);
                    }
                }
            }
        }

        $expression = $this->replaceExpression($product, $expression, 'p');
        if ($expression === null) {
            return null;
        }
        $expression = $this->replaceExpression($customerSession->getCustomer(), $expression, 'c');
        if ($expression === null) {
            return null;
        }
        $expression = $this->replaceExpression($customerSession->getCustomerPandaEquity(), $expression, 'e');
        if ($expression === null) {
            return null;
        }
        #$expression = ' {sd.qty_ordered}  {sm.qty_ordered}  {sy.qty_ordered} ';

        if (stripos($expression, '{sd.') !== false) {
            $stats = $this->getBestSeller($product->getSku(), 'daily');
            $expression = $this->replaceExpression($stats, $expression, 'sd');
            if ($expression === null) {
                return null;
            }
        }

        if (stripos($expression, '{sm.') !== false) {
            $stats = $this->getBestSeller($product->getSku(), 'monthly');
            $expression = $this->replaceExpression($stats, $expression, 'sm');
            if ($expression === null) {
                return null;
            }
        }

        if (stripos($expression, '{sy.') !== false) {
            $stats = $this->getBestSeller($product->getSku(), 'yearly');
            $expression = $this->replaceExpression($stats, $expression, 'sy');
            if ($expression === null) {
                return null;
            }
        }
        #if ($this->evaluate) {

        $result = $this->evaluateExpression($expression);

        if (is_float($result) || is_numeric($result)) {
            if ($scope == 'global') {
                $this->cacheManager->getFrontend()->save((string) $result, $cacheKey);
            }

            $customerSession->setData($cacheKey, $result);

            return $result;
        }

        #}

        return null;
    }

    /**
     * @param \Magento\Catalog\Model\Product   $product
     * @param \Magento\Customer\Model\Customer $customer
     * @param                                  $expression
     *
     * @return null
     */
    public function getEvaluatedProductPriceExpressionTest(
        \Magento\Catalog\Model\Product $product,
        \Magento\Customer\Model\Customer $customer,
        $expression
    ) {

        if ($customer->getId()) {
            $equity = $this->kpisFactory->create()->load($customer->getId(), 'customer_id');
        }

        $expression = preg_replace('/[\x00-\x1F\x7F]/u', '', $expression);
        if (stripos($expression, '{{if') !== false) {
            preg_match_all(self::CONSTRUCTION_IF_PATTERN, $expression, $constructions, PREG_SET_ORDER);

            foreach ($constructions as $eval) {
                $ifCondition = $eval[1];

                $ifCondition = $this->replaceExpressionIf($product, $ifCondition, 'p');

                if ($customer->getId()) {
                    $ifCondition = $this->replaceExpressionIf($customer, $ifCondition, 'c');
                    $ifCondition = $this->replaceExpressionIf($equity, $ifCondition, 'e');
                }

                if (preg_match('/[a-z]/si', $ifCondition)) {
                    $expression = $this->replaceInExpression($eval[0], '', $expression);
                } else {
                    $ifCondition = preg_replace('/[^0-9()&><=.]/si', '', $ifCondition);

                    #$ifCondition = eval('return ' . $ifCondition . ' ;');
                    $ifCondition = $this->evaluateExpression($ifCondition);

                    if (!$ifCondition && isset($eval[4])) {
                        $expression = $this->replaceInExpression($eval[0], $eval[4], $expression);
                    } elseif ($ifCondition) {
                        $expression = $this->replaceInExpression($eval[0], $eval[2], $expression);
                    } elseif (!$ifCondition) {
                        $expression = $this->replaceInExpression($eval[0], '', $expression);
                    }
                }
            }
        }

        $expression = $this->replaceExpression($product, $expression, 'p');
        if ($expression === null) {
            return null;
        }
        if ($customer->getId()) {
            $expression = $this->replaceExpression($customer, $expression, 'c');
            if ($expression === null) {
                return null;
            }
            $expression = $this->replaceExpression($equity, $expression, 'e');
            if ($expression === null) {
                return null;
            }
        }

        if (stripos($expression, '{sd.') !== false) {
            $stats = $this->getBestSeller($product->getSku(), 'daily');
            $expression = $this->replaceExpression($stats, $expression, 'sd');
            if ($expression === null) {
                return null;
            }
        }

        if (stripos($expression, '{sm.') !== false) {
            $stats = $this->getBestSeller($product->getSku(), 'monthly');
            $expression = $this->replaceExpression($stats, $expression, 'sm');
            if ($expression === null) {
                return null;
            }
        }

        if (stripos($expression, '{sy.') !== false) {
            $stats = $this->getBestSeller($product->getSku(), 'yearly');
            $expression = $this->replaceExpression($stats, $expression, 'sy');
            if ($expression === null) {
                return null;
            }
        }

        $result = $this->evaluateExpression($expression);

        if (is_float($result) || is_numeric($result)) {
            $return = ['result' => $result];

            if (isset($equity)) {
                $return['kpis'] = $equity->getData();
            }

            return $return;
        }

        return null;
    }

    /**
     * @param $needle
     * @param $replace
     * @param $haystack
     *
     * @return mixed
     */
    public function replaceInExpression($needle, $replace, $haystack)
    {

        $pos = strpos($haystack, $needle);
        if ($pos !== false) {
            $haystack = substr_replace($haystack, $replace, $pos, strlen($needle));
        }

        return $haystack;
    }

    /**
     * @param $model
     * @param $expression
     * @param $code
     *
     * @return mixed
     */
    public function replaceExpressionIf($model, $expression, $code)
    {

        if (stripos($expression, $code . '.') !== false) {
            $this->evaluate = true;
            preg_match_all('/' . $code . '\.([a-zA-Z_]+)+/si', $expression, $resultProduct);

            foreach ($resultProduct[1] as $key => $item) {
                $resultProduct[1][$key] = $model->getData($item);
            }

            $expression = str_replace($resultProduct[0], $resultProduct[1], $expression);
        }

        return $expression;
    }

    /**
     * @param $model
     * @param $expression
     * @param $code
     *
     * @return mixed
     */
    public function replaceExpression($model, $expression, $code)
    {

        if (stripos($expression, '{' . $code . '.') !== false) {
            $this->evaluate = true;

            preg_match_all('/\{' . $code . '\.(.*?)\}/si', $expression, $resultProduct);

            foreach ($resultProduct[1] as $key => $item) {
                if (null === $model->getData($item)) {
                    return null;
                }
                $resultProduct[1][$key] = floatval($model->getData($item));
            }

            $expression = str_replace($resultProduct[0], $resultProduct[1], $expression);
        }

        return $expression;
    }

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     *
     * @return mixed
     */
    public function loadPandaEquity(\Magento\Customer\Model\Session $customerSession)
    {

        if (!$customerSession->getCustomerPandaEquity()) {
            $data = $this->kpisFactory->create()->load($customerSession->getCustomerId(), 'customer_id');
            $customerSession->setCustomerPandaEquity($data);
        }

        return $customerSession->getCustomerPandaEquity();
    }

    /**
     * @param $m
     *
     * @return mixed
     * @copyright  http://stackoverflow.com/questions/928563/evaluating-a-string-of-simple-mathematical-expressions#929681
     *
     */
    private function callback1($m)
    {

        return $this->evaluateExpression($m[1]);
    }

    /**
     * @param $n
     * @param $m
     *
     * @return float
     * @copyright http://stackoverflow.com/questions/928563/evaluating-a-string-of-simple-mathematical-expressions#929681
     *
     */
    private function callback2($n, $m)
    {

        $o = $m[0];
        $m[0] = ' ';

        return $o == '+' ? $n + $m : ($o == '-' ? $n - $m : ($o == '*' ? $n * $m : $n / $m));
    }

    /**
     * @param $s
     *
     * @return mixed
     * @copyright http://stackoverflow.com/questions/928563/evaluating-a-string-of-simple-mathematical-expressions#929681
     */
    public function evaluateExpression($s)
    {

        while ($s !=
               ($t = preg_replace_callback('/\(([^()]*)\)/', [$this, 'callback1'], $s))) {
            $s = $t;
        }
        preg_match_all('![-+/*].*?[\d.]+!', "+$s", $m);

        return array_reduce($m[0], [$this, 'callback2']);
    }
}
