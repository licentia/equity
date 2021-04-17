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

namespace Licentia\Equity\Model;

use Licentia\Equity\Api\Data\KpisInterface;

/**
 * Class Kpis
 *
 * @package Licentia\Panda\Model
 */
class Kpis extends \Magento\Framework\Model\AbstractModel implements KpisInterface
{

    /**
     * @var string
     */
    protected $_eventPrefix = 'panda_customers_kpis';

    /**
     * @var string
     */
    protected $_eventObject = 'kpis';

    /**
     * @var
     */
    protected $kpiDescription = [
        'email_meta'                      => ['title' => 'Email', 'type' => 'string'],
        'abandoned'                       => ['title' => 'Number of days with an abandoned cart', 'type' => 'number'],
        'abandoned_date'                  => ['title' => 'Abandoned Cart Date', 'type' => 'string'],
        'cart_totals'                     => ['title' => 'Abandoned Cart Total', 'type' => 'currency'],
        'cart_number'                     => ['title' => 'Abandoned Cart Qty Products', 'type' => 'number'],
        'cart_products'                   => ['title' => 'Abandoned Cart N. Products', 'type' => 'number'],
        'anniversary'                     => ['title' => 'Days until Anniversary', 'type' => 'number'],
        'age'                             => ['title' => 'Age', 'type' => 'number'],
        'dob'                             => ['title' => 'Date of Birth', 'type' => 'date'],
        'gender'                          => ['title' => 'Gender', 'type' => 'string'],
        'pending_payment'                 => ['title' => 'Days With Pending Payment for Order', 'type' => 'string'],
        'pending_payment_date'            => ['title' => 'Order Date with Pending Payment', 'type' => 'date'],
        'last_order'                      => ['title' => 'Days since Last Order', 'type' => 'string'],
        'last_order_date'                 => ['title' => 'Last Order Date', 'type' => 'date'],
        'first_order'                     => ['title' => 'Days Since First Order', 'type' => 'string'],
        'first_order_date'                => ['title' => 'First Order Date', 'type' => 'date'],
        'last_review'                     => ['title' => 'Days Since Last Review', 'type' => 'string'],
        'last_review_date'                => ['title' => 'Last Review Date', 'type' => 'date'],
        'account'                         => ['title' => 'Days since Registration', 'type' => 'string'],
        'account_date'                    => ['title' => 'Account Registration Date', 'type' => 'date'],
        'number_reviews'                  => ['title' => 'Number of Reviews', 'type' => 'number'],
        'number_orders'                   => ['title' => 'Number of Orders', 'type' => 'number'],
        'number_completed_orders'         => ['title' => 'Number of Completed Orders', 'type' => 'number'],
        'percentage_complete_orders'      => ['title' => 'Percentage of Completed Orders', 'type' => 'number'],
        'order_amount'                    => ['title' => 'Total Orders Amount', 'type' => 'currency'],
        'order_average'                   => ['title' => 'Orders Average', 'type' => 'currency'],
        'percentage_order_amount'         => ['title' => 'Percentage Orders Amount', 'type' => 'number'],
        'percentage_orders_with_discount' => ['title' => 'Percentage Orders With Discount', 'type' => 'number'],
        'number_visits'                   => ['title' => 'Number of Visits', 'type' => 'number'],
        'order_average_1year'             => ['title' => 'Orders Average Last year', 'type' => 'currency'],
        'order_amount_1year'              => ['title' => 'Orders Amount Last Year', 'type' => 'currency'],
        'order_average_older'             => ['title' => 'Orders Average before Last Year', 'type' => 'currency'],
        'order_amount_older'              => ['title' => 'Orders Amount before Last Year', 'type' => 'currency'],
        'order_average_days'              => ['title' => 'Average Days between Orders', 'type' => 'number'],
        'number_orders_with_discount'     => ['title' => 'Number of Orders with Discount', 'type' => 'number'],
        'shipping'                        => ['title' => 'Total Amount Shipping', 'type' => 'currency'],
        'taxes'                           => ['title' => 'Total Amount Taxes', 'type' => 'currency'],
        'subtotal'                        => ['title' => 'Total Amount Subtotal', 'type' => 'currency'],
        'discount'                        => ['title' => 'Total Amount Discount', 'type' => 'currency'],
        'cost'                            => ['title' => 'Total Amount Cost', 'type' => 'currency'],
        'profit'                          => ['title' => 'Total Amount Profit', 'type' => 'currency'],
        'refunded'                        => ['title' => 'Total Amount Refunded', 'type' => 'currency'],
        'last_activity_date'              => ['title' => 'Last Activity Date', 'type' => 'date'],
        'last_activity'                   => ['title' => 'Days Since Last Activity', 'type' => 'string'],
        'formula_1'                       => ['title' => 'Formula Result 1', 'type' => 'currency'],
        'formula_2'                       => ['title' => 'Formula Result 2', 'type' => 'currency'],
        'formula_3'                       => ['title' => 'Formula Result 3', 'type' => 'currency'],
        'formula_4'                       => ['title' => 'Formula Result 4', 'type' => 'currency'],
        'formula_5'                       => ['title' => 'Formula Result 5', 'type' => 'currency'],
        'formula_6'                       => ['title' => 'Formula Result 6', 'type' => 'currency'],
        'formula_7'                       => ['title' => 'Formula Result 7', 'type' => 'currency'],
        'formula_8'                       => ['title' => 'Formula Result 8', 'type' => 'currency'],
        'formula_9'                       => ['title' => 'Formula Result 9', 'type' => 'currency'],
        'formula_10'                      => ['title' => 'Formula Result 10', 'type' => 'currency'],
    ];

    /**
     * @var Formulas
     */
    protected $formulas;

    /**
     * Kpis constructor.
     *
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param FormulasFactory                                              $formulasFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        FormulasFactory $formulasFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );

        $this->formulas = $formulasFactory->create()->getFormulas();
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\Kpis::class);
    }

    /**
     * @param     $arr
     * @param     $col
     * @param int $dir
     */
    public function arraySortByColumn(&$arr, $col, $dir = SORT_ASC)
    {

        $sort_col = [];
        foreach ($arr as $key => $row) {
            $sort_col[$key] = $row[$col];
        }

        array_multisort($sort_col, $dir, $arr);
    }

    /**
     * @param $email
     *
     * @return Kpis
     */
    public function loadByEmail($email)
    {

        return $this->load($email, 'email_meta');
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {

        $options = $this->kpiDescription;
        $return = [];
        $this->arraySortByColumn($options, 'title');

        foreach ($options as $key => $option) {
            if ($this->formulas->getData($key)) {
                $option['title'] = $this->formulas->getData($key);
            }

            $return[] = ['label' => __($option['title']), 'value' => $key];
        }

        return $return;
    }

    /**
     * @return array
     */
    public function getKpisDescription()
    {

        return $this->kpiDescription;
    }

    /**
     * @param $key
     *
     * @return array|mixed
     */
    public function getKpiInfo($key)
    {

        if (substr($key, 0, 9) == 'formula_') {
            if ($this->formulas->getData($key)) {
                return ['title' => $this->formulas->getData($key), 'type' => 'currency'];
            }
        }

        if (isset($this->kpiDescription[$key])) {
            $this->kpiDescription[$key]['title'] = __($this->kpiDescription[$key]['title']);
        }

        return isset($this->kpiDescription[$key]) ? $this->kpiDescription[$key] : ['title' => '', 'type' => ''];
    }

    /**
     * @return $this
     */
    public function buildCustomerAttributesPredictions()
    {

        $resource = $this->getResource();
        $connection = $resource->getConnection();

        $attributes = ['gender' => 'panda_gender_prediction', 'age' => 'panda_age_prediction'];

        foreach ($attributes as $attributeKey => $attribute) {
            $columns = [
                'email'       => 'so.customer_email',
                'count'       => 'COUNT(*)',
                $attributeKey => "GROUP_CONCAT(DISTINCT(cpev.`value`))",
            ];

            $query = $connection->select()
                                ->from(['soi' => $resource->getTable('sales_order_item')], [])
                                ->join(['so' => $resource->getTable('sales_order')], 'soi.order_id = so.entity_id', [])
                                ->join(
                                    ['cpe' => $resource->getTable('catalog_product_entity')],
                                    'soi.sku = cpe.sku',
                                    []
                                )
                                ->join(
                                    ['cpev' => $resource->getTable('catalog_product_entity_varchar')],
                                    'cpe.entity_id = cpev.entity_id',
                                    []
                                )
                                ->join(
                                    ['eav' => $resource->getTable('eav_attribute')],
                                    'cpev.attribute_id = eav.attribute_id',
                                    []
                                )
                                ->columns($columns)
                                ->where('eav.attribute_code=?', $attribute)
                                ->where('so.state IN (?)', ['complete', 'closed'])
                                ->group('so.customer_email')
                                ->group('cpev.value')
                                ->order('so.customer_email')
                                ->order('cpev.value');

            $customers = $connection->fetchAll($query);

            $attributePrediction = [];
            for ($i = 0; $i < count($customers); $i++) {
                $agesTmp = explode(',', $customers[$i][$attributeKey]);
                $agesTmpCount = array_count_values($agesTmp);

                $attributePrediction[$customers[$i]['email']][] = $agesTmpCount;
            }

            foreach ($attributePrediction as $email => $row) {
                foreach ($row as $rowId => $item) {
                    if ($rowId == 0) {
                        continue;
                    }

                    foreach ($item as $key => $value) {
                        if (isset($attributePrediction[$email][0][$key])) {
                            $attributePrediction[$email][0][$key] += $value;
                        } else {
                            $attributePrediction[$email][0][$key] = $value;
                        }
                    }

                    unset($attributePrediction[$email][$rowId]);
                }

                asort($attributePrediction[$email][0]);

                $predictedAttribute = array_keys($attributePrediction[$email][0]);
                $predictedAttribute = array_pop($predictedAttribute);

                $connection->update(
                    $resource->getTable('panda_customers_kpis'),
                    [
                        'predicted_' . $attributeKey => $predictedAttribute,
                    ],
                    ['email_meta=?' => $email]
                );
            }
        }

        return $this;
    }

    /**
     * Get kpi_id
     *
     * @return string
     */
    public function getKpiId()
    {

        return $this->getData(self::KPI_ID);
    }

    /**
     * Set kpi_id
     *
     * @param string $kpi_id
     *
     * @return Kpis
     */
    public function setKpiId($kpi_id)
    {

        return $this->setData(self::KPI_ID, $kpi_id);
    }

    /**
     * Get Number of Visits
     *
     * @return string
     */
    public function getNumberVisits()
    {

        return $this->getData(self::NUMBER_VISITS);
    }

    /**
     * Set Number of Visits
     *
     * @param string $numberOfVisits
     *
     * @return Kpis
     */
    public function setNumberVisits($numberOfVisits)
    {

        return $this->setData(self::NUMBER_VISITS, $numberOfVisits);
    }

    /**
     * Get PercentageCompleteOrders
     *
     * @return string
     */
    public function getPercentageOrdersWithDiscount()
    {

        return $this->getData(self::PERCENTAGE_ORDERS_WITH_DISCOUNT);
    }

    /**
     * Set $ordersWithDiscount
     *
     * @param string $ordersWithDiscount
     *
     * @return Kpis
     */
    public function setPercentageOrdersWithDiscount($ordersWithDiscount)
    {

        return $this->setData(self::PERCENTAGE_ORDERS_WITH_DISCOUNT, $ordersWithDiscount);
    }

    /**
     * Get customer_id
     *
     * @return string
     */
    public function getCustomerId()
    {

        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * Set customer_id
     *
     * @param string $customer_id
     *
     * @return Kpis
     */
    public function setCustomerId($customer_id)
    {

        return $this->setData(self::CUSTOMER_ID, $customer_id);
    }

    /**
     * @param string $name
     *
     * @return $this|KpisInterface
     */
    public function setCustomerName($name)
    {

        return $this->setData(self::CUSTOMER_NAME, $name);
    }

    /**
     * @return mixed
     */
    public function getCustomerName()
    {

        return $this->getData(self::CUSTOMER_NAME);
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {

        return $this->getData(self::GENDER);
    }

    /**
     * Set $gender
     *
     * @param string $gender
     *
     * @return Kpis
     */
    public function setGender($gender)
    {

        return $this->setData(self::GENDER, $gender);
    }

    /**
     * Get email_meta
     *
     * @return string
     */
    public function getEmailMeta()
    {

        return $this->getData(self::EMAIL_META);
    }

    /**
     * Set email_meta
     *
     * @param string $email_meta
     *
     * @return Kpis
     */
    public function setEmailMeta($email_meta)
    {

        return $this->setData(self::EMAIL_META, $email_meta);
    }

    /**
     * Get subtotal
     *
     * @return string
     */
    public function getSubtotal()
    {

        return $this->getData(self::SUBTOTAL);
    }

    /**
     * Set subtotal
     *
     * @param string $subtotal
     *
     * @return Kpis
     */
    public function setSubtotal($subtotal)
    {

        return $this->setData(self::SUBTOTAL, $subtotal);
    }

    /**
     * Get profit
     *
     * @return string
     */
    public function getProfit()
    {

        return $this->getData(self::PROFIT);
    }

    /**
     * Set profit
     *
     * @param string $profit
     *
     * @return Kpis
     */
    public function setProfit($profit)
    {

        return $this->setData(self::PROFIT, $profit);
    }

    /**
     * Get subtotal
     *
     * @return string
     */
    public function getCost()
    {

        return $this->getData(self::COST);
    }

    /**
     * Set cost
     *
     * @param string $cost
     *
     * @return Kpis
     */
    public function setCost($cost)
    {

        return $this->setData(self::COST, $cost);
    }

    /**
     * Get store_id_meta
     *
     * @return string
     */
    public function getStoreIdMeta()
    {

        return $this->getData(self::STORE_ID_META);
    }

    /**
     * Set store_id_meta
     *
     * @param string $store_id_meta
     *
     * @return Kpis
     */
    public function setStoreIdMeta($store_id_meta)
    {

        return $this->setData(self::STORE_ID_META, $store_id_meta);
    }

    /**
     * Get abandoned
     *
     * @return string
     */
    public function getAbandoned()
    {

        return $this->getData(self::ABANDONED);
    }

    /**
     * Set abandoned
     *
     * @param string $abandoned
     *
     * @return Kpis
     */
    public function setAbandoned($abandoned)
    {

        return $this->setData(self::ABANDONED, $abandoned);
    }

    /**
     * Get abandoned_date
     *
     * @return string
     */
    public function getAbandonedDate()
    {

        return $this->getData(self::ABANDONED_DATE);
    }

    /**
     * Set abandoned_date
     *
     * @param string $abandoned_date
     *
     * @return Kpis
     */
    public function setAbandonedDate($abandoned_date)
    {

        return $this->setData(self::ABANDONED_DATE, $abandoned_date);
    }

    /**
     * Get cart_totals
     *
     * @return string
     */
    public function getCartTotals()
    {

        return $this->getData(self::CART_TOTALS);
    }

    /**
     * Set cart_totals
     *
     * @param string $cart_totals
     *
     * @return Kpis
     */
    public function setCartTotals($cart_totals)
    {

        return $this->setData(self::CART_TOTALS, $cart_totals);
    }

    /**
     * Get cart_number
     *
     * @return string
     */
    public function getCartNumber()
    {

        return $this->getData(self::CART_NUMBER);
    }

    /**
     * Set cart_number
     *
     * @param string $cart_number
     *
     * @return Kpis
     */
    public function setCartNumber($cart_number)
    {

        return $this->setData(self::CART_NUMBER, $cart_number);
    }

    /**
     * Get cart_products
     *
     * @return string
     */
    public function getCartProducts()
    {

        return $this->getData(self::CART_PRODUCTS);
    }

    /**
     * Set cart_products
     *
     * @param string $cart_products
     *
     * @return Kpis
     */
    public function setCartProducts($cart_products)
    {

        return $this->setData(self::CART_PRODUCTS, $cart_products);
    }

    /**
     * Get anniversary
     *
     * @return string
     */
    public function getAnniversary()
    {

        return $this->getData(self::ANNIVERSARY);
    }

    /**
     * Set anniversary
     *
     * @param string $anniversary
     *
     * @return Kpis
     */
    public function setAnniversary($anniversary)
    {

        return $this->setData(self::ANNIVERSARY, $anniversary);
    }

    /**
     * Get age
     *
     * @return string
     */
    public function getAge()
    {

        return $this->getData(self::AGE);
    }

    /**
     * Set age
     *
     * @param string $age
     *
     * @return Kpis
     */
    public function setAge($age)
    {

        return $this->setData(self::AGE, $age);
    }

    /**
     * Get dob
     *
     * @return string
     */
    public function getDob()
    {

        return $this->getData(self::DOB);
    }

    /**
     * Set dob
     *
     * @param string $dob
     *
     * @return Kpis
     */
    public function setDob($dob)
    {

        return $this->setData(self::DOB, $dob);
    }

    /**
     * Get pending_payment
     *
     * @return string
     */
    public function getPendingPayment()
    {

        return $this->getData(self::PENDING_PAYMENT);
    }

    /**
     * Set pending_payment
     *
     * @param string $pending_payment
     *
     * @return Kpis
     */
    public function setPendingPayment($pending_payment)
    {

        return $this->setData(self::PENDING_PAYMENT, $pending_payment);
    }

    /**
     * Get pending_payment_date
     *
     * @return string
     */
    public function getPendingPaymentDate()
    {

        return $this->getData(self::PENDING_PAYMENT_DATE);
    }

    /**
     * Set pending_payment_date
     *
     * @param string $pending_payment_date
     *
     * @return Kpis
     */
    public function setPendingPaymentDate($pending_payment_date)
    {

        return $this->setData(self::PENDING_PAYMENT_DATE, $pending_payment_date);
    }

    /**
     * Get last_order
     *
     * @return string
     */
    public function getLastOrder()
    {

        return $this->getData(self::LAST_ORDER);
    }

    /**
     * Set last_order
     *
     * @param string $last_order
     *
     * @return Kpis
     */
    public function setLastOrder($last_order)
    {

        return $this->setData(self::LAST_ORDER, $last_order);
    }

    /**
     * Get last_order_date
     *
     * @return string
     */
    public function getLastOrderDate()
    {

        return $this->getData(self::LAST_ORDER_DATE);
    }

    /**
     * Set last_order_date
     *
     * @param string $last_order_date
     *
     * @return Kpis
     */
    public function setLastOrderDate($last_order_date)
    {

        return $this->setData(self::LAST_ORDER_DATE, $last_order_date);
    }

    /**
     * Get first_order
     *
     * @return string
     */
    public function getFirstOrder()
    {

        return $this->getData(self::FIRST_ORDER);
    }

    /**
     * Set first_order
     *
     * @param string $first_order
     *
     * @return Kpis
     */
    public function setFirstOrder($first_order)
    {

        return $this->setData(self::FIRST_ORDER, $first_order);
    }

    /**
     * Get first_order_date
     *
     * @return string
     */
    public function getFirstOrderDate()
    {

        return $this->getData(self::FIRST_ORDER_DATE);
    }

    /**
     * Set first_order_date
     *
     * @param string $first_order_date
     *
     * @return Kpis
     */
    public function setFirstOrderDate($first_order_date)
    {

        return $this->setData(self::FIRST_ORDER_DATE, $first_order_date);
    }

    /**
     * Get last_review
     *
     * @return string
     */
    public function getLastReview()
    {

        return $this->getData(self::LAST_REVIEW);
    }

    /**
     * Set last_review
     *
     * @param string $last_review
     *
     * @return Kpis
     */
    public function setLastReview($last_review)
    {

        return $this->setData(self::LAST_REVIEW, $last_review);
    }

    /**
     * Get last_review_date
     *
     * @return string
     */
    public function getLastReviewDate()
    {

        return $this->getData(self::LAST_REVIEW_DATE);
    }

    /**
     * Set last_review_date
     *
     * @param string $last_review_date
     *
     * @return Kpis
     */
    public function setLastReviewDate($last_review_date)
    {

        return $this->setData(self::LAST_REVIEW_DATE, $last_review_date);
    }

    /**
     * Get account
     *
     * @return string
     */
    public function getAccount()
    {

        return $this->getData(self::ACCOUNT);
    }

    /**
     * Set account
     *
     * @param string $account
     *
     * @return Kpis
     */
    public function setAccount($account)
    {

        return $this->setData(self::ACCOUNT, $account);
    }

    /**
     * Get account_date
     *
     * @return string
     */
    public function getAccountDate()
    {

        return $this->getData(self::ACCOUNT_DATE);
    }

    /**
     * Set account_date
     *
     * @param string $account_date
     *
     * @return Kpis
     */
    public function setAccountDate($account_date)
    {

        return $this->setData(self::ACCOUNT_DATE, $account_date);
    }

    /**
     * Get number_reviews
     *
     * @return string
     */
    public function getNumberReviews()
    {

        return $this->getData(self::NUMBER_REVIEWS);
    }

    /**
     * Set number_reviews
     *
     * @param string $number_reviews
     *
     * @return Kpis
     */
    public function setNumberReviews($number_reviews)
    {

        return $this->setData(self::NUMBER_REVIEWS, $number_reviews);
    }

    /**
     * Get number_orders
     *
     * @return string
     */
    public function getNumberOrders()
    {

        return $this->getData(self::NUMBER_ORDERS);
    }

    /**
     * Set number_orders
     *
     * @param string $number_orders
     *
     * @return Kpis
     */
    public function setNumberOrders($number_orders)
    {

        return $this->setData(self::NUMBER_ORDERS, $number_orders);
    }

    /**
     * Get number_orders
     *
     * @return string
     */
    public function getNumberCompletedOrders()
    {

        return $this->getData(self::NUMBER_COMPLETED_ORDERS);
    }

    /**
     * Set number_orders
     *
     * @param string $number_orders
     *
     * @return Kpis
     */
    public function setNumberCompletedOrders($number_orders)
    {

        return $this->setData(self::NUMBER_COMPLETED_ORDERS, $number_orders);
    }

    /**
     * Get percentage_complete_orders
     *
     * @return string
     */
    public function getPercentageCompleteOrders()
    {

        return $this->getData(self::PERCENTAGE_COMPLETE_ORDERS);
    }

    /**
     * Set percentage_complete_orders
     *
     * @param string $percentage_complete_orders
     *
     * @return Kpis
     */
    public function setPercentageCompleteOrders($percentage_complete_orders)
    {

        return $this->setData(self::PERCENTAGE_COMPLETE_ORDERS, $percentage_complete_orders);
    }

    /**
     * Get order_amount
     *
     * @return string
     */
    public function getOrderAmount()
    {

        return $this->getData(self::ORDER_AMOUNT);
    }

    /**
     * Set order_amount
     *
     * @param string $order_amount
     *
     * @return Kpis
     */
    public function setOrderAmount($order_amount)
    {

        return $this->setData(self::ORDER_AMOUNT, $order_amount);
    }

    /**
     * Get order_average
     *
     * @return string
     */
    public function getOrderAverage()
    {

        return $this->getData(self::ORDER_AVERAGE);
    }

    /**
     * Set order_average
     *
     * @param string $order_average
     *
     * @return Kpis
     */
    public function setOrderAverage($order_average)
    {

        return $this->setData(self::ORDER_AVERAGE, $order_average);
    }

    /**
     * Get percentage_order_amount
     *
     * @return string
     */
    public function getPercentageOrderAmount()
    {

        return $this->getData(self::PERCENTAGE_ORDER_AMOUNT);
    }

    /**
     * Set percentage_order_amount
     *
     * @param string $percentage_order_amount
     *
     * @return Kpis
     */
    public function setPercentageOrderAmount($percentage_order_amount)
    {

        return $this->setData(self::PERCENTAGE_ORDER_AMOUNT, $percentage_order_amount);
    }

    /**
     * Get order_average_1year
     *
     * @return string
     */
    public function getOrderAverage1year()
    {

        return $this->getData(self::ORDER_AVERAGE_1YEAR);
    }

    /**
     * Set order_average_1year
     *
     * @param string $order_average_1year
     *
     * @return Kpis
     */
    public function setOrderAverage1year($order_average_1year)
    {

        return $this->setData(self::ORDER_AVERAGE_1YEAR, $order_average_1year);
    }

    /**
     * Get order_amount_1year
     *
     * @return string
     */
    public function getOrderAmount1year()
    {

        return $this->getData(self::ORDER_AMOUNT_1YEAR);
    }

    /**
     * Set order_amount_1year
     *
     * @param string $order_amount_1year
     *
     * @return Kpis
     */
    public function setOrderAmount1year($order_amount_1year)
    {

        return $this->setData(self::ORDER_AMOUNT_1YEAR, $order_amount_1year);
    }

    /**
     * Get order_average_older
     *
     * @return string
     */
    public function getOrderAverageOlder()
    {

        return $this->getData(self::ORDER_AVERAGE_OLDER);
    }

    /**
     * Set order_average_older
     *
     * @param string $order_average_older
     *
     * @return Kpis
     */
    public function setOrderAverageOlder($order_average_older)
    {

        return $this->setData(self::ORDER_AVERAGE_OLDER, $order_average_older);
    }

    /**
     * Get order_amount_older
     *
     * @return string
     */
    public function getOrderAmountOlder()
    {

        return $this->getData(self::ORDER_AMOUNT_OLDER);
    }

    /**
     * Set order_amount_older
     *
     * @param string $order_amount_older
     *
     * @return Kpis
     */
    public function setOrderAmountOlder($order_amount_older)
    {

        return $this->setData(self::ORDER_AMOUNT_OLDER, $order_amount_older);
    }

    /**
     * Get order_average_days
     *
     * @return string
     */
    public function getOrderAverageDays()
    {

        return $this->getData(self::ORDER_AVERAGE_DAYS);
    }

    /**
     * Set order_average_days
     *
     * @param string $order_average_days
     *
     * @return Kpis
     */
    public function setOrderAverageDays($order_average_days)
    {

        return $this->setData(self::ORDER_AVERAGE_DAYS, $order_average_days);
    }

    /**
     * Get shipping
     *
     * @return string
     */
    public function getShipping()
    {

        return $this->getData(self::SHIPPING);
    }

    /**
     * Set shipping
     *
     * @param string $shipping
     *
     * @return Kpis
     */
    public function setShipping($shipping)
    {

        return $this->setData(self::SHIPPING, $shipping);
    }

    /**
     * Get taxes
     *
     * @return string
     */
    public function getTaxes()
    {

        return $this->getData(self::TAXES);
    }

    /**
     * Set taxes
     *
     * @param string $taxes
     *
     * @return Kpis
     */
    public function setTaxes($taxes)
    {

        return $this->setData(self::TAXES, $taxes);
    }

    /**
     * Get discount
     *
     * @return string
     */
    public function getDiscount()
    {

        return $this->getData(self::DISCOUNT);
    }

    /**
     * Set discount
     *
     * @param string $discount
     *
     * @return Kpis
     */
    public function setDiscount($discount)
    {

        return $this->setData(self::DISCOUNT, $discount);
    }

    /**
     * Get refunded
     *
     * @return string
     */
    public function getRefunded()
    {

        return $this->getData(self::REFUNDED);
    }

    /**
     * Set refunded
     *
     * @param string $refunded
     *
     * @return Kpis
     */
    public function setRefunded($refunded)
    {

        return $this->setData(self::REFUNDED, $refunded);
    }

    /**
     * Get refunded
     *
     * @return string
     */
    public function getNumberOrdersWithDiscount()
    {

        return $this->getData(self::NUMBER_ORDERS_WITH_DISCOUNT);
    }

    /**
     * Set refunded
     *
     * @param string $number
     *
     * @return Kpis
     */
    public function setNumberOrdersWithDiscount($number)
    {

        return $this->setData(self::NUMBER_ORDERS_WITH_DISCOUNT, $number);
    }

    /**
     * Get last activity
     *
     * @return string
     */
    public function getLastActivity()
    {

        return $this->getData(self::LAST_ACTIVITY);
    }

    /**
     * Set last activity
     *
     * @param string $activity
     *
     * @return Kpis
     */
    public function setLastActivity($activity)
    {

        return $this->setData(self::LAST_ACTIVITY, $activity);
    }

    /**
     * Get last activity
     *
     * @return string
     */
    public function getLastActivityDate()
    {

        return $this->getData(self::LAST_ACTIVITY_DATE);
    }

    /**
     * Set last activity
     *
     * @param string $activity
     *
     * @return Kpis
     */
    public function setLastActivityDate($activity)
    {

        return $this->setData(self::LAST_ACTIVITY_DATE, $activity);
    }

    /**
     * Get formula_1
     *
     * @return string
     */
    public function getFormula1()
    {

        return $this->getData(self::FORMULA_1);
    }

    /**
     * Set formula_1
     *
     * @param string $formula_1
     *
     * @return Kpis
     */
    public function setFormula1($formula_1)
    {

        return $this->setData(self::FORMULA_1, $formula_1);
    }

    /**
     * Get formula_2
     *
     * @return string
     */
    public function getFormula2()
    {

        return $this->getData(self::FORMULA_2);
    }

    /**
     * Set formula_2
     *
     * @param string $formula_2
     *
     * @return Kpis
     */
    public function setFormula2($formula_2)
    {

        return $this->setData(self::FORMULA_2, $formula_2);
    }

    /**
     * Get formula_3
     *
     * @return string
     */
    public function getFormula3()
    {

        return $this->getData(self::FORMULA_3);
    }

    /**
     * Set formula_3
     *
     * @param string $formula_3
     *
     * @return Kpis
     */
    public function setFormula3($formula_3)
    {

        return $this->setData(self::FORMULA_3, $formula_3);
    }

    /**
     * Get formula_4
     *
     * @return string
     */
    public function getFormula4()
    {

        return $this->getData(self::FORMULA_4);
    }

    /**
     * Set formula_4
     *
     * @param string $formula_4
     *
     * @return Kpis
     */
    public function setFormula4($formula_4)
    {

        return $this->setData(self::FORMULA_4, $formula_4);
    }

    /**
     * Get formula_5
     *
     * @return string
     */
    public function getFormula5()
    {

        return $this->getData(self::FORMULA_5);
    }

    /**
     * Set formula_5
     *
     * @param string $formula_5
     *
     * @return Kpis
     */
    public function setFormula5($formula_5)
    {

        return $this->setData(self::FORMULA_5, $formula_5);
    }

    /**
     * @param $predictedAge
     *
     * @return $this
     */
    public function setPredictedAge($predictedAge)
    {

        return $this->setData('predicted_age', $predictedAge);
    }

    /**
     * @param $predictedGender
     *
     * @return $this
     */
    public function setPredictedGender($predictedGender)
    {

        return $this->setData('predicted_gender', $predictedGender);
    }

    /**
     * @return mixed
     * @todo interface
     */
    public function getPredictedAge()
    {

        return $this->getData('predicted_age');
    }

    /**
     * @return mixed
     */
    public function getPredictedGender()
    {

        return $this->getData('predicted_gender');
    }
}
