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
 * @modified   03/06/20, 16:19 GMT
 *
 */

namespace Licentia\Equity\Api\Data;

/**
 * Interface KpisInterface
 *
 * @package Licentia\Panda\Api\Data
 */
interface KpisInterface
{

    /**
     *
     */
    const GENDER = 'gender';

    /**
     *
     */
    const NUMBER_VISITS = 'number_visits';

    /**
     *
     */
    const PERCENTAGE_ORDERS_WITH_DISCOUNT = 'percentage_orders_with_discount';

    /**
     *
     */
    const LAST_REVIEW_DATE = 'last_review_date';

    /**
     *
     */
    const LAST_ACTIVITY_DATE = 'last_activity_date';

    /**
     *
     */
    const LAST_ACTIVITY = 'last_activity';

    /**
     *
     */
    const NUMBER_ORDERS_WITH_DISCOUNT = 'number_orders_with_discount';

    /**
     *
     */
    const ORDER_AMOUNT = 'order_amount';

    /**
     *
     */
    const SUBTOTAL = 'subtotal';

    /**
     *
     */
    const KPI_ID = 'kpi_id';

    /**
     *
     */
    const CART_PRODUCTS = 'cart_products';

    /**
     *
     */
    const ANNIVERSARY = 'anniversary';

    /**
     *
     */
    const FIRST_ORDER = 'first_order';

    /**
     *
     */
    const PENDING_PAYMENT = 'pending_payment';

    /**
     *
     */
    const LAST_ORDER_DATE = 'last_order_date';

    /**
     *
     */
    const EMAIL_META = 'email_meta';

    /**
     *
     */
    const CART_NUMBER = 'cart_number';

    /**
     *
     */
    const FIRST_ORDER_DATE = 'first_order_date';

    /**
     *
     */
    const ABANDONED = 'abandoned';

    /**
     *
     */
    const LAST_REVIEW = 'last_review';

    /**
     *
     */
    const CUSTOMER_ID = 'customer_id';

    /**
     *
     */
    const CUSTOMER_NAME = 'customer_name';

    /**
     *
     */
    const PENDING_PAYMENT_DATE = 'pending_payment_date';

    /**
     *
     */
    const DOB = 'dob';

    /**
     *
     */
    const CART_TOTALS = 'cart_totals';

    /**
     *
     */
    const AGE = 'age';

    /**
     *
     */
    const STORE_ID_META = 'store_id_meta';

    /**
     *
     */
    const ABANDONED_DATE = 'abandoned_date';

    /**
     *
     */
    const LAST_ORDER = 'last_order';

    /**
     *
     */
    const ACCOUNT = 'account';

    /**
     *
     */
    const ACCOUNT_DATE = 'account_date';

    /**
     *
     */
    const NUMBER_REVIEWS = 'number_reviews';

    /**
     *
     */
    const NUMBER_ORDERS = 'number_orders';

    /**
     *
     */
    const NUMBER_COMPLETED_ORDERS = 'number_completed_orders';

    /**
     *
     */
    const PERCENTAGE_COMPLETE_ORDERS = 'percentage_complete_orders';

    /**
     *
     */
    const ORDER_AVERAGE = 'order_average';

    /**
     *
     */
    const PERCENTAGE_ORDER_AMOUNT = 'percentage_order_amount';

    /**
     *
     */
    const ORDER_AVERAGE_1YEAR = 'order_average_1year';

    /**
     *
     */
    const ORDER_AMOUNT_1YEAR = 'order_amount_1year';

    /**
     *
     */
    const ORDER_AVERAGE_OLDER = 'order_average_older';

    /**
     *
     */
    const ORDER_AMOUNT_OLDER = 'order_amount_older';

    /**
     *
     */
    const ORDER_AVERAGE_DAYS = 'order_average_days';

    /**
     *
     */
    const SHIPPING = 'shipping';

    /**
     *
     */
    const TAXES = 'taxes';

    /**
     *
     */
    const DISCOUNT = 'discount';

    /**
     *
     */
    const REFUNDED = 'refunded';

    /**
     *
     */
    const PROFIT = 'profit';

    /**
     *
     */
    const COST = 'cost';

    /**
     *
     */
    const FORMULA_1 = 'formula_1';

    /**
     *
     */
    const FORMULA_2 = 'formula_2';

    /**
     *
     */
    const FORMULA_3 = 'formula_3';

    /**
     *
     */
    const FORMULA_4 = 'formula_4';

    /**
     *
     */
    const FORMULA_5 = 'formula_5';

    /**
     * Get kpi_id
     *
     * @return string|null
     */

    public function getKpiId();

    /**
     * Set kpi_id
     *
     * @param string $kpi_id
     *
     * @return KpisInterface
     */

    public function setKpiId($kpi_id);

    /**
     * Get $numberVisits
     *
     * @return string|null
     */

    public function getNumberVisits();

    /**
     * Set $numberVisits
     *
     * @param string $numberVisits
     *
     * @return KpisInterface
     */

    public function setNumberVisits($numberVisits);

    /**
     * Get getPercentageOrdersWithDiscount
     *
     * @return string|null
     */

    public function getPercentageOrdersWithDiscount();

    /**
     * Set $percentageOrdersWithDiscount
     *
     * @param string $percentageOrdersWithDiscount
     *
     * @return KpisInterface
     */

    public function setPercentageOrdersWithDiscount($percentageOrdersWithDiscount);

    /**
     * Get customer_id
     *
     * @return string|null
     */

    public function getCustomerId();

    /**
     * Set customer_id
     *
     * @param string $customer_id
     *
     * @return KpisInterface
     */

    public function setCustomerId($customer_id);

    /**
     * Get gender
     *
     * @return string|null
     */

    public function getGender();

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return KpisInterface
     */

    public function setGender($gender);

    /**
     * Get Customer Name
     *
     * @return string|null
     */

    public function getCustomerName();

    /**
     * Set name
     *
     * @param string $name
     *
     * @return KpisInterface
     */

    public function setCustomerName($name);

    /**
     * Get email_meta
     *
     * @return string|null
     */

    public function getEmailMeta();

    /**
     * Set email_meta
     *
     * @param string $email_meta
     *
     * @return KpisInterface
     */

    public function setEmailMeta($email_meta);

    /**
     * Get subtotal
     *
     * @return string|null
     */

    public function getSubtotal();

    /**
     * Set subtotal
     *
     * @param string $subtotal
     *
     * @return KpisInterface
     */

    public function setSubtotal($subtotal);

    /**
     * Get profit
     *
     * @return string|null
     */

    public function getProfit();

    /**
     * Set profit
     *
     * @param string $profit
     *
     * @return KpisInterface
     */

    public function setProfit($profit);

    /**
     * Get cost
     *
     * @return string|null
     */

    public function getCost();

    /**
     * Set cost
     *
     * @param string $cost
     *
     * @return KpisInterface
     */

    public function setCost($cost);

    /**
     * Get store_id_meta
     *
     * @return string|null
     */

    public function getStoreIdMeta();

    /**
     * Set store_id_meta
     *
     * @param string $store_id_meta
     *
     * @return KpisInterface
     */

    public function setStoreIdMeta($store_id_meta);

    /**
     * Get abandoned
     *
     * @return string|null
     */

    public function getAbandoned();

    /**
     * Set abandoned
     *
     * @param string $abandoned
     *
     * @return KpisInterface
     */

    public function setAbandoned($abandoned);

    /**
     * Get abandoned_date
     *
     * @return string|null
     */

    public function getAbandonedDate();

    /**
     * Set abandoned_date
     *
     * @param string $abandoned_date
     *
     * @return KpisInterface
     */

    public function setAbandonedDate($abandoned_date);

    /**
     * Get cart_totals
     *
     * @return string|null
     */

    public function getCartTotals();

    /**
     * Set cart_totals
     *
     * @param string $cart_totals
     *
     * @return KpisInterface
     */

    public function setCartTotals($cart_totals);

    /**
     * Get cart_number
     *
     * @return string|null
     */

    public function getCartNumber();

    /**
     * Set cart_number
     *
     * @param string $cart_number
     *
     * @return KpisInterface
     */

    public function setCartNumber($cart_number);

    /**
     * Get cart_products
     *
     * @return string|null
     */

    public function getCartProducts();

    /**
     * Set cart_products
     *
     * @param string $cart_products
     *
     * @return KpisInterface
     */

    public function setCartProducts($cart_products);

    /**
     * Get anniversary
     *
     * @return string|null
     */

    public function getAnniversary();

    /**
     * Set anniversary
     *
     * @param string $anniversary
     *
     * @return KpisInterface
     */

    public function setAnniversary($anniversary);

    /**
     * Get age
     *
     * @return string|null
     */

    public function getAge();

    /**
     * Set age
     *
     * @param string $age
     *
     * @return KpisInterface
     */

    public function setAge($age);

    /**
     * Get dob
     *
     * @return string|null
     */

    public function getDob();

    /**
     * Set dob
     *
     * @param string $dob
     *
     * @return KpisInterface
     */

    public function setDob($dob);

    /**
     * Get pending_payment
     *
     * @return string|null
     */

    public function getPendingPayment();

    /**
     * Set pending_payment
     *
     * @param string $pending_payment
     *
     * @return KpisInterface
     */

    public function setPendingPayment($pending_payment);

    /**
     * Get pending_payment_date
     *
     * @return string|null
     */

    public function getPendingPaymentDate();

    /**
     * Set pending_payment_date
     *
     * @param string $pending_payment_date
     *
     * @return KpisInterface
     */

    public function setPendingPaymentDate($pending_payment_date);

    /**
     * Get last_order
     *
     * @return string|null
     */

    public function getLastOrder();

    /**
     * Set last_order
     *
     * @param string $last_order
     *
     * @return KpisInterface
     */

    public function setLastOrder($last_order);

    /**
     * Get last_order_date
     *
     * @return string|null
     */

    public function getLastOrderDate();

    /**
     * Set last_order_date
     *
     * @param string $last_order_date
     *
     * @return KpisInterface
     */

    public function setLastOrderDate($last_order_date);

    /**
     * Get first_order
     *
     * @return string|null
     */

    public function getFirstOrder();

    /**
     * Set first_order
     *
     * @param string $first_order
     *
     * @return KpisInterface
     */

    public function setFirstOrder($first_order);

    /**
     * Get first_order_date
     *
     * @return string|null
     */

    public function getFirstOrderDate();

    /**
     * Set first_order_date
     *
     * @param string $first_order_date
     *
     * @return KpisInterface
     */

    public function setFirstOrderDate($first_order_date);

    /**
     * Get last_review
     *
     * @return string|null
     */

    public function getLastReview();

    /**
     * Set last_review
     *
     * @param string $last_review
     *
     * @return KpisInterface
     */

    public function setLastReview($last_review);

    /**
     * Get last_review_date
     *
     * @return string|null
     */

    public function getLastReviewDate();

    /**
     * Set last_review_date
     *
     * @param string $last_review_date
     *
     * @return KpisInterface
     */

    public function setLastReviewDate($last_review_date);

    /**
     * Get account
     *
     * @return string|null
     */

    public function getAccount();

    /**
     * Set account
     *
     * @param string $account
     *
     * @return KpisInterface
     */

    public function setAccount($account);

    /**
     * Get Last Activity
     *
     * @return string|null
     */

    public function getLastActivity();

    /**
     * Set activity days
     *
     * @param string $activity
     *
     * @return KpisInterface
     */

    public function setLastActivity($activity);

    /**
     * Get activity
     *
     * @return string|null
     */

    public function getLastActivityDate();

    /**
     * Set activity date
     *
     * @param string $activity
     *
     * @return KpisInterface
     */

    public function setLastActivityDate($activity);

    /**
     * Get account_date
     *
     * @return string|null
     */

    public function getAccountDate();

    /**
     * Set account_date
     *
     * @param string $account_date
     *
     * @return KpisInterface
     */

    public function setAccountDate($account_date);

    /**
     * Get number_reviews
     *
     * @return string|null
     */

    public function getNumberReviews();

    /**
     * Set number_reviews
     *
     * @param string $number_reviews
     *
     * @return KpisInterface
     */

    public function setNumberReviews($number_reviews);

    /**
     * Get number_orders
     *
     * @return string|null
     */

    public function getNumberOrders();

    /**
     * Set number_orders
     *
     * @param string $number_orders
     *
     * @return KpisInterface
     */

    public function setNumberOrders($number_orders);

    /**
     * Get number_completed_orders
     *
     * @return string|null
     */

    public function getNumberCompletedOrders();

    /**
     * Set number_compelted_orders
     *
     * @param string $number_completed_orders
     *
     * @return KpisInterface
     */

    public function setNumberCompletedOrders($number_completed_orders);

    /**
     * Get percentage_complete_orders
     *
     * @return string|null
     */

    public function getPercentageCompleteOrders();

    /**
     * Set percentage_complete_orders
     *
     * @param string $percentage_complete_orders
     *
     * @return KpisInterface
     */

    public function setPercentageCompleteOrders($percentage_complete_orders);

    /**
     * Get order_amount
     *
     * @return string|null
     */

    public function getOrderAmount();

    /**
     * Set order_amount
     *
     * @param string $order_amount
     *
     * @return KpisInterface
     */

    public function setOrderAmount($order_amount);

    /**
     * Get order_average
     *
     * @return string|null
     */

    public function getOrderAverage();

    /**
     * Set order_average
     *
     * @param string $order_average
     *
     * @return KpisInterface
     */

    public function setOrderAverage($order_average);

    /**
     * Get percentage_order_amount
     *
     * @return string|null
     */

    public function getPercentageOrderAmount();

    /**
     * Set percentage_order_amount
     *
     * @param string $percentage_order_amount
     *
     * @return KpisInterface
     */

    public function setPercentageOrderAmount($percentage_order_amount);

    /**
     * Get order_average_1year
     *
     * @return string|null
     */

    public function getOrderAverage1year();

    /**
     * Set order_average_1year
     *
     * @param string $order_average_1year
     *
     * @return KpisInterface
     */

    public function setOrderAverage1year($order_average_1year);

    /**
     * Get order_amount_1year
     *
     * @return string|null
     */

    public function getOrderAmount1year();

    /**
     * Set order_amount_1year
     *
     * @param string $order_amount_1year
     *
     * @return KpisInterface
     */

    public function setOrderAmount1year($order_amount_1year);

    /**
     * Get order_average_older
     *
     * @return string|null
     */

    public function getOrderAverageOlder();

    /**
     * Set order_average_older
     *
     * @param string $order_average_older
     *
     * @return KpisInterface
     */

    public function setOrderAverageOlder($order_average_older);

    /**
     * Get order_amount_older
     *
     * @return string|null
     */

    public function getOrderAmountOlder();

    /**
     * Set order_amount_older
     *
     * @param string $order_amount_older
     *
     * @return KpisInterface
     */

    public function setOrderAmountOlder($order_amount_older);

    /**
     * Get order_average_days
     *
     * @return string|null
     */

    public function getOrderAverageDays();

    /**
     * Set order_average_days
     *
     * @param string $order_average_days
     *
     * @return KpisInterface
     */

    public function setOrderAverageDays($order_average_days);

    /**
     * Get shipping
     *
     * @return string|null
     */

    public function getShipping();

    /**
     * Set shipping
     *
     * @param string $shipping
     *
     * @return KpisInterface
     */

    public function setShipping($shipping);

    /**
     * Get taxes
     *
     * @return string|null
     */

    public function getTaxes();

    /**
     * Set taxes
     *
     * @param string $taxes
     *
     * @return KpisInterface
     */

    public function setTaxes($taxes);

    /**
     * Get discount
     *
     * @return string|null
     */

    public function getDiscount();

    /**
     * Set discount
     *
     * @param string $discount
     *
     * @return KpisInterface
     */

    public function setDiscount($discount);

    /**
     * Get refunded
     *
     * @return string|null
     */

    public function getRefunded();

    /**
     * Set refunded
     *
     * @param string $refunded
     *
     * @return KpisInterface
     */

    public function setRefunded($refunded);

    /**
     * Get Number orders with discount
     *
     * @return string|null
     */

    public function getNumberOrdersWithDiscount();

    /**
     * Set Number orders with discount
     *
     * @param string $number
     *
     * @return KpisInterface
     */

    public function setNumberOrdersWithDiscount($number);

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
     * @return KpisInterface
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
     * @return KpisInterface
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
     * @return KpisInterface
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
     * @return KpisInterface
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
     * @return KpisInterface
     */

    public function setFormula5($formula_5);
}
