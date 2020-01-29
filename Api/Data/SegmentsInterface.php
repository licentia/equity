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

namespace Licentia\Equity\Api\Data;

/**
 * Interface SegmentsInterface
 *
 * @package Licentia\Panda\Api\Data
 */
interface SegmentsInterface
{

    const MANUAL = 'manual';

    const FORMULA_1 = 'formula_1';

    const FORMULA_2 = 'formula_2';

    const FORMULA_3 = 'formula_3';

    const FORMULA_4 = 'formula_4';

    const FORMULA_5 = 'formula_5';

    const MANUALLY_ADDED = 'manually_added';

    const IS_ACTIVE = 'is_active';

    const RECORDS = 'records';

    const CRON = 'cron';

    const LAST_UPDATE = 'last_update';

    const NAME = 'name';

    const DESCRIPTION = 'description';

    const TYPE = 'type';

    const SEGMENT_ID = 'segment_id';

    const REAL_TIME_CRON = 'real_time_update_cron';

    /**
     * Get segment_id
     *
     * @return string|null
     */

    public function getSegmentId();

    /**
     * Set segment_id
     *
     * @param int $segment_id
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */

    public function setSegmentId($segment_id);

    /**
     * Get name
     *
     * @return string|null
     */

    public function getName();

    /**
     * Set name
     *
     * @param string $name
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */

    public function setName($name);

    /**
     * Get is_active
     *
     * @return int|null
     */

    public function getIsActive();

    /**
     * Set is_active
     *
     * @param int $is_active
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */

    public function setIsActive($is_active);

    /**
     * Get records
     *
     * @return int|null
     */

    public function getRecords();

    /**
     * Set records
     *
     * @param int $records
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */

    public function setRecords($records);

    /**
     * Get description
     *
     * @return string|null
     */

    public function getDescription();

    /**
     * Set description
     *
     * @param string $description
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */

    public function setDescription($description);

    /**
     * Get cron
     *
     * @return string|null
     */

    public function getCron();

    /**
     * Set cron
     *
     * @param string $cron
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */

    public function setCron($cron);

    /**
     * Get last_update
     *
     * @return string|null
     */

    public function getLastUpdate();

    /**
     * Set last_update
     *
     * @param string $last_update
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */

    public function setLastUpdate($last_update);

    /**
     * Get type
     *
     * @return string|null
     */

    public function getType();

    /**
     * Set type
     *
     * @param string $type
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */

    public function setType($type);

    /**
     * Get manual
     *
     * @return int|null
     */

    public function getManual();

    /**
     * Set manual
     *
     * @param int $manual
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */

    public function setManual($manual);

    /**
     * Get manually_added
     *
     * @return int|null
     */

    public function getManuallyAdded();

    /**
     * Set manually_added
     *
     * @param int $manually_added
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */

    public function setManuallyAdded($manually_added);

    /**
     * Get formula_1
     *
     * @return string|null
     */

    public function getFormula1();

    /**
     * Set formula_1
     *
     * @param string $formula_1
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */

    public function setFormula1($formula_1);

    /**
     * Get formula_2
     *
     * @return string|null
     */

    public function getFormula2();

    /**
     * Set formula_2
     *
     * @param string $formula_2
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */

    public function setFormula2($formula_2);

    /**
     * Get formula_3
     *
     * @return string|null
     */

    public function getFormula3();

    /**
     * Set formula_3
     *
     * @param string $formula_3
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */

    public function setFormula3($formula_3);

    /**
     * Get formula_4
     *
     * @return string|null
     */

    public function getFormula4();

    /**
     * Set formula_4
     *
     * @param string $formula_4
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */

    public function setFormula4($formula_4);

    /**
     * Get formula_5
     *
     * @return string|null
     */

    public function getFormula5();

    /**
     * Set formula_5
     *
     * @param string $formula_5
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */

    public function setFormula5($formula_5);

    /**
     * Get RealTimeUpdateCron
     *
     * @return bool|null
     */

    public function getRealTimeUpdateCron();

    /**
     * Set RealTimeUpdateCron
     *
     * @param bool $RealTimeUpdateCron
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */

    public function setRealTimeUpdateCron($RealTimeUpdateCron);

    /**
     * @param $conditionsSerialized
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setConditionsSerialized($conditionsSerialized);

    /**
     * Set is_active
     *
     * @param string $run
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */

    public function setRun($run);

    /**
     * Set is_active
     *
     * @param string $cronLastRun
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */

    public function setCronLastRun($cronLastRun);

    /**
     * @param string $build
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setBuild($build);

    /**
     * @param int $notifyUser
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setNotifyUser($notifyUser);

    /**
     * @param string $extraData
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setExtraData($extraData);

    /**
     * @param string $websitesIds
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setWebsitesIds($websitesIds);

    /**
     * @param string $productsRelations
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setProductsRelations($productsRelations);

    /**
     * @param string $formula_0
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setFormula0($formula_0);

    /**
     * @param string $formula_6
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setFormula6($formula_6);

    /**
     * @param string $formula_7
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setFormula7($formula_7);

    /**
     * @param string $formula_8
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setFormula8($formula_8);

    /**
     * @param string $formula_9
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setFormula9($formula_9);

    /**
     * @param string $formula_10
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setFormula10($formula_10);

    /**
     * @param string $numberOrders
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setNumberOrders($numberOrders);

    /**
     * @param string $numberCompletedOrders
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setNumberCompletedOrders($numberCompletedOrders);

    /**
     * @param double $orderAmount
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setOrderAmount($orderAmount);

    /**
     * @param double $orderAverage
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setOrderAverage($orderAverage);

    /**
     * @param int $percentageCompleteOrders
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setPercentageCompleteOrders($percentageCompleteOrders);

    /**
     * @param int $percentageOrderAmount
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setPercentageOrderAmount($percentageOrderAmount);

    /**
     * @param double $orderAverage_1year
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setOrderAverage1year($orderAverage_1year);

    /**
     * @param double $orderAmount_1year
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setOrderAmount1year($orderAmount_1year);

    /**
     * @param double $orderAverageOlder
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setOrderAverageOlder($orderAverageOlder);

    /**
     * @param double $orderAmountOlder
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setOrderAmountOlder($orderAmountOlder);

    /**
     * @param int $orderAverageDays
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setOrderAverageDays($orderAverageDays);

    /**
     * @param double $numberOrdersWithDiscount
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setNumberOrdersWithDiscount($numberOrdersWithDiscount);

    /**
     * @param double $shipping
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setShipping($shipping);

    /**
     * @param double $taxes
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setTaxes($taxes);

    /**
     * @param double $subtotal
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setSubtotal($subtotal);

    /**
     * @param double $discount
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setDiscount($discount);

    /**
     * @param double $cost
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setCost($cost);

    /**
     * @param double $profit
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setProfit($profit);

    /**
     * @param double $refunded
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setRefunded($refunded);

    /**
     * @param int $lastOrder
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setLastOrder($lastOrder);

    /**
     * @param int $firstOrder
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setFirstOrder($firstOrder);

    /**
     * @param int $lastActivity
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setLastActivity($lastActivity);

    /**
     * @param int $age
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setAge($age);

    /**
     * @param int $abandoned
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setAbandoned($abandoned);

    /**
     * @param double $cartTotals
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setCartTotals($cartTotals);

    /**
     * @param int $cartNumber
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setCartNumber($cartNumber);

    /**
     * @param int $cartProducts
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setCartProducts($cartProducts);

    /**
     * @param int $pendingPayment
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setPendingPayment($pendingPayment);

    /**
     * @param int $account
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setAccount($account);

    /**
     * @param int $lastReview
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setLastReview($lastReview);

    /**
     * @param int $numberReviews
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setNumberReviews($numberReviews);

    /**
     * @param string $skuBought
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setSkuBought($skuBought);

    /**
     * @param bool $affectsOrder
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setAffectsOrder($affectsOrder);

    /**
     * @param bool $affectsProduct
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setAffectsProduct($affectsProduct);

    /**
     * @param bool $affectsQuote
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setAffectsQuote($affectsQuote);

    /**
     * @param bool $affectsAccount
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setAffectsAccount($affectsAccount);

    /**
     * @param bool $affectsReview
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setAffectsReview($affectsReview);

    /**
     * @param bool $affectsSearch
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setAffectsSearch($affectsSearch);

    /**
     * @param bool $affectsSubscriber
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setAffectsSubscriber($affectsSubscriber);

    /**
     * @param bool $affectsUpdate
     *
     * @return \Licentia\Equity\Api\Data\SegmentsInterface
     */
    public function setAffectsUpdate($affectsUpdate);

    /**
     * Get conditions_serialized
     *
     * @return string|null
     */
    public function getConditionsSerialized();

    /**
     * Get run
     *
     * @return string|null
     */
    public function getRun();

    /**
     * Get cron_last_run
     *
     * @return string|null
     */
    public function getCronLastRun();

    /**
     * Get build
     *
     * @return string|null
     */
    public function getBuild();

    /**
     * Get notify_user
     *
     * @return int|null
     */
    public function getNotifyUser();

    /**
     * Get extra_data
     *
     * @return string|null
     */
    public function getExtraData();

    /**
     * Get websites_ids
     *
     * @return string|null
     */
    public function getWebsitesIds();

    /**
     * Get product_relations
     *
     * @return string|null
     */
    public function getProductsRelations();

    /**
     * Get formula_0
     *
     * @return string|null
     */
    public function getFormula0();

    /**
     * Get formula_6
     *
     * @return string|null
     */
    public function getFormula6();

    /**
     * Get formula_7
     *
     * @return string|null
     */
    public function getFormula7();

    /**
     * Get formula_8
     *
     * @return string|null
     */
    public function getFormula8();

    /**
     * Get formula_9
     *
     * @return string|null
     */
    public function getFormula9();

    /**
     * Get formula_10
     *
     * @return string|null
     */
    public function getFormula10();

    /**
     * Get number_orders
     *
     * @return int|null
     */
    public function getNumberOrders();

    /**
     * Get number_completed_orders
     *
     * @return int|null
     */
    public function getNumberCompletedOrders();

    /**
     * Get order_amount
     *
     * @return double|null
     */
    public function getOrderAmount();

    /**
     * Get order_average
     *
     * @return double|null
     */
    public function getOrderAverage();

    /**
     * Get percentage_complete_orders
     *
     * @return int|null
     */
    public function getPercentageCompleteOrders();

    /**
     * Get percentage_order_amount
     *
     * @return int|null
     */
    public function getPercentageOrderAmount();

    /**
     * Get order_average_1year
     *
     * @return double|null
     */
    public function getOrderAverage1year();

    /**
     * Get order_amount_1year
     *
     * @return double|null
     */
    public function getOrderAmount1year();

    /**
     * Get order_average_older
     *
     * @return double|null
     */
    public function getOrderAverageOlder();

    /**
     * Get order_amount_older
     *
     * @return double|null
     */
    public function getOrderAmountOlder();

    /**
     * Get order_average_days
     *
     * @return int|null
     */
    public function getOrderAverageDays();

    /**
     * Get number_orders_with_discount
     *
     * @return int|null
     */
    public function getNumberOrdersWithDiscount();

    /**
     * Get shipping
     *
     * @return double|null
     */
    public function getShipping();

    /**
     *
     * Get taxes
     *
     * @return double|null
     */
    public function getTaxes();

    /**
     * Get subtotal
     *
     * @return double|null
     */
    public function getSubtotal();

    /**
     * Get discount
     *
     * @return double|null
     */
    public function getDiscount();

    /**
     * Get cost
     *
     * @return double|null
     */
    public function getCost();

    /**
     * Get profit
     *
     * @return double|null
     */
    public function getProfit();

    /**
     * Get refunded
     *
     * @return double|null
     */
    public function getRefunded();

    /**
     * Get last_order
     *
     * @return int|null
     */
    public function getLastOrder();

    /**
     * Get first_order
     *
     * @return int|null
     */
    public function getFirstOrder();

    /**
     * Get last_activity
     *
     * @return int|null
     */
    public function getLastActivity();

    /**
     * Get age
     *
     * @return int|null
     */
    public function getAge();

    /**
     * Get abandoned
     *
     * @return int|null
     */
    public function getAbandoned();

    /**
     * Get cart_totals
     *
     * @return double|null
     */
    public function getCartTotals();

    /**
     * Get cart_number
     *
     * @return int|null
     */
    public function getCartNumber();

    /**
     * Get cart_products
     *
     * @return int|null
     */
    public function getCartProducts();

    /**
     * Get pending_payment
     *
     * @return int|null
     */
    public function getPendingPayment();

    /**
     * Get account
     *
     * @return int|null
     */
    public function getAccount();

    /**
     * Get last view
     *
     * @return int|null
     */
    public function getLastReview();

    /**
     * Get number reviews
     *
     * @return int|null
     */
    public function getNumberReviews();

    /**
     * Get sku bought
     *
     * @return string|null
     */
    public function getSkuBought();

    /**
     * Get affetcs_orders
     *
     * @return bool|null
     */
    public function getAffectsOrder();

    /**
     * Get affetcs_product
     *
     * @return bool|null
     */
    public function getAffectsProduct();

    /**
     * Get affects_quote
     *
     * @return bool|null
     */
    public function getAffectsQuote();

    /**
     * Get affects_account
     *
     * @return bool|null
     */
    public function getAffectsAccount();

    /**
     * Get affects_review
     *
     * @return bool|null
     */
    public function getAffectsReview();

    /**
     * Get affects_search
     *
     * @return bool|null
     */
    public function getAffectsSearch();

    /**
     * Get affects_susbcriber
     *
     * @return bool|null
     */
    public function getAffectsSubscriber();

    /**
     * Get affects_update
     *
     * @return bool|null
     */
    public function getAffectsUpdate();
}
