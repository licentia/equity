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

use Licentia\Equity\Api\Data\FormulasInterface;

/**
 * Class Formulas
 *
 * @package Licentia\Panda\Model
 */
class Formulas extends \Magento\Framework\Model\AbstractModel implements FormulasInterface
{

    const TOTAL_FORMULAS = 10;

    /**
     *
     */
    const ALLOWED_FIELDS = [
        'last_order'                  => 'AVG(last_order)',
        'last_activity'               => 'AVG(last_activity)',
        'age'                         => 'AVG(age)',
        'first_order'                 => 'AVG(first_order)',
        'abandoned'                   => 'AVG(abandoned)',
        'account'                     => 'AVG(account)',
        'number_reviews'              => 'AVG(number_reviews)',
        'number_orders'               => 'AVG(number_orders)',
        'number_completed_orders'     => 'AVG(number_completed_orders)',
        'percentage_complete_orders'  => 'AVG(percentage_complete_orders)',
        'order_amount'                => 'AVG(order_amount)',
        'order_average'               => 'AVG(order_average)',
        'percentage_order_amount'     => 'AVG(percentage_order_amount)',
        'order_average_1year'         => 'AVG(order_average_1year)',
        'order_amount_1year'          => 'AVG(order_amount_1year)',
        'order_average_older'         => 'AVG(order_average_older)',
        'order_amount_older'          => 'AVG(order_amount_older)',
        'order_average_days'          => 'AVG(order_average_days)',
        'number_orders_with_discount' => 'AVG(number_orders_with_discount)',
        'shipping'                    => 'AVG(shipping)',
        'taxes'                       => 'AVG(taxes)',
        'subtotal'                    => 'AVG(subtotal)',
        'discount'                    => 'AVG(discount)',
        'profit'                      => 'AVG(profit)',
        'cost'                        => 'AVG(cost)',
        'refunded'                    => 'AVG(refunded)',
        'cart_totals'                 => 'AVG(cart_totals)',
        'cart_number'                 => 'AVG(cart_number)',
        'cart_products'               => 'AVG(cart_products)',
        'pending_payment'             => 'AVG(pending_payment)',
        'last_review'                 => 'AVG(last_review)',
    ];

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_formulas';

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var SegmentsFactory
     */
    protected $segmentsFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected $pandaHelper;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\Formulas::class);
    }

    /**
     * Formulas constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface         $timezone
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $scopeConfig
     * @param SegmentsFactory                                              $segmentsFactory
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Licentia\Equity\Helper\Data                                 $pandaHelper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        SegmentsFactory $segmentsFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Licentia\Equity\Helper\Data $pandaHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->storeManager = $storeManager;
        $this->timezone = $timezone;
        $this->scopeConfig = $scopeConfig;
        $this->segmentsFactory = $segmentsFactory;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     * @return false|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    public function getConnection()
    {

        return $this->pandaHelper->getConnection();
    }

    /**
     * @return $this|\Magento\Framework\DataObject
     * @throws \Exception
     */
    public function getFormulas()
    {

        $total = $this->getCollection();

        if ($total->getSize() == 1) {
            return $total->getFirstItem();
        }

        $total->walk('delete');

        $data = [];
        $data['formula_id'] = 1;
        $data['formula_1'] = '';
        $data['formula_2'] = '';
        $data['formula_3'] = '';
        $data['formula_4'] = '';
        $data['formula_5'] = '';
        $data['formula_6'] = '';
        $data['formula_7'] = '';
        $data['formula_8'] = '';
        $data['formula_9'] = '';
        $data['formula_10'] = '';
        $data['formula_1_name'] = 'Formula 1';
        $data['formula_2_name'] = 'Formula 2';
        $data['formula_3_name'] = 'Formula 3';
        $data['formula_4_name'] = 'Formula 4';
        $data['formula_5_name'] = 'Formula 5';
        $data['formula_6_name'] = 'Formula 6';
        $data['formula_7_name'] = 'Formula 7';
        $data['formula_8_name'] = 'Formula 8';
        $data['formula_9_name'] = 'Formula 9';
        $data['formula_10_name'] = 'Formula 10';

        return $this->setData($data)
                    ->save()->load(1);
    }

    /**
     * @return string
     */
    public function getTotalCustomers()
    {

        return $this->getConnection()->fetchOne(
            $this->getConnection()->select()
                 ->from(
                     $this->getTable('customer_entity'),
                     ['count' => 'COUNT(*)']
                 )
        );
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     *
     * @return string
     */
    public function getTotalOrders(\Magento\Framework\DB\Select $select)
    {

        return $this->getConnection()->fetchOne(
            $select->from($this->getTable('sales_order'), ['count' => 'COUNT(*)'])
        );
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     *
     * @return int
     */
    public function getNumberOrdersAvg(\Magento\Framework\DB\Select $select)
    {

        $total = $this->getConnection()->fetchCol(
            $select->from(
                $this->getTable('sales_order'),
                ['AVG' => 'COUNT(sales_order.base_grand_total)']
            )
                   ->group('customer_email')
        );

        if (count($total) == 0) {
            return 0;
        }

        return array_sum($total) / count($total);
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     *
     * @return string
     */
    public function getTotalOrdersAvg(\Magento\Framework\DB\Select $select)
    {

        return $this->getConnection()->fetchOne(
            $select->from(
                $this->getTable('sales_order'),
                ['AVG' => 'AVG(base_grand_total * base_to_global_rate)']
            )
        );
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     *
     * @return string
     */
    public function getTotalAmountOrders(\Magento\Framework\DB\Select $select)
    {

        return $this->getConnection()->fetchOne(
            $select->from(
                $this->getTable('sales_order'),
                ['sum' => 'SUM(base_grand_total * base_to_global_rate)']
            )
        );
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     *
     * @return string
     */
    public function getTotalAmountRefunded(\Magento\Framework\DB\Select $select)
    {

        return $this->getConnection()->fetchOne(
            $select->from(
                $this->getTable('sales_order'),
                ['sum' => 'SUM(base_total_refunded * base_to_global_rate)']
            )
        );
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     *
     * @return string
     */
    public function getTotalCompletedOrders(\Magento\Framework\DB\Select $select)
    {

        return $this->getConnection()->fetchOne(
            $select->from(
                $this->getTable('sales_order'),
                ['return' => new \Zend_Db_Expr('COUNT(*)')]
            )
        );
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     *
     * @return string
     */
    public function getTotalAmountShipping(\Magento\Framework\DB\Select $select)
    {

        return $this->getConnection()->fetchOne(
            $select->from(
                $this->getTable('sales_order'),
                ['return' => new \Zend_Db_Expr('SUM(base_shipping_amount * base_to_global_rate)')]
            )
        );
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     *
     * @return string
     */
    public function getTotalAmountTaxes(\Magento\Framework\DB\Select $select)
    {

        return $this->getConnection()->fetchOne(
            $select->from(
                $this->getTable('sales_order'),
                ['return' => new \Zend_Db_Expr('SUM(base_tax_amount * base_to_global_rate)')]
            )
        );
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     * @param \Magento\Framework\DB\Select $selectSegment
     * @param bool                         $currentPeriod
     *
     * @return string
     */
    public function getTotalAmountProfit(
        \Magento\Framework\DB\Select $select,
        \Magento\Framework\DB\Select $selectSegment,
        $currentPeriod = false
    ) {

        $pandaSalesTable = $this->getTable('panda_sales_extra_costs');

        if ($currentPeriod) {
            return $this->getConnection()->fetchOne(
                $select->from(
                    $this->getTable('sales_order'),
                    [
                        'return' => new \Zend_Db_Expr(
                            sprintf(
                                'SUM((%s - %s - %s - %s - %s - %s - %s) * %s)',
                                $this->getConnection()->getIfNullSql('base_total_paid', 0),
                                $this->getConnection()->getIfNullSql('base_total_refunded', 0),
                                $this->getConnection()->getIfNullSql('base_tax_invoiced', 0),
                                $this->getConnection()->getIfNullSql('base_shipping_invoiced', 0),
                                $this->getConnection()->getIfNullSql('base_total_invoiced_cost', 0),
                                $this->getConnection()->getIfNullSql('panda_shipping_cost', 0),
                                "(select SUM(investment) from `$pandaSalesTable`)",
                                $this->getConnection()->getIfNullSql('base_to_global_rate', 0)
                            )
                        ),
                    ]
                )
            );
        }

        $period = $this->scopeConfig->getValue(
            'panda_equity/equity/period',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
        if ($period == 'm') {
            $interval = 'MONTH';
        } else {
            $interval = 'YEAR';
        }

        $selectSegment->from(
            $this->getTable('sales_order'),
            [
                'return' => new \Zend_Db_Expr(
                    "COUNT(*)"
                ),
            ]
        )
                      ->where('panda_acquisition_campaign IS NOT NULL')
                      ->where(
                          "created_at >= DATE(? - INTERVAL 1 $interval)",
                          $this->pandaHelper->gmtDate()
                      );

        $totalOrdersSegment = $this->getConnection()->fetchOne($selectSegment);

        $selectTotal = clone $select;
        $selectTotal->from(
            $this->getTable('panda_sales_extra_costs'),
            [
                'return' => new \Zend_Db_Expr(
                    "SUM(investment)"
                ),
            ]
        )
                    ->where('panda_acquisition_campaign IS NOT NULL');

        $totalOrdersGlobal = $this->getConnection()->fetchOne($selectTotal);

        $selectSales = $this->getConnection()->select();
        $selectSales->from($this->getTable('sales_order'))
                    ->where(
                        "from_date <= DATE(? - INTERVAL 1 $interval)",
                        $this->pandaHelper->gmtDate()
                    );

        $totalCost = $this->getConnection()->fetchOne($selectSales);

        $imputableCost = $totalCost * $totalOrdersSegment / $totalOrdersGlobal;

        return $this->getConnection()->fetchOne(
            $select->from(
                $this->getTable('sales_order'),
                [
                    'return' => new \Zend_Db_Expr(
                        sprintf(
                            'SUM((%s - %s - %s - %s - %s - %s - %s) * %s)',
                            $this->getConnection()->getIfNullSql('base_total_paid', 0),
                            $this->getConnection()->getIfNullSql('base_total_refunded', 0),
                            $this->getConnection()->getIfNullSql('base_tax_invoiced', 0),
                            $this->getConnection()->getIfNullSql('base_shipping_invoiced', 0),
                            $this->getConnection()->getIfNullSql('base_total_invoiced_cost', 0),
                            $this->getConnection()->getIfNullSql('panda_shipping_cost', 0),
                            $imputableCost,
                            $this->getConnection()->getIfNullSql('base_to_global_rate', 0)
                        )
                    ),
                ]
            )
        );
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     *
     * @return string
     */
    public function getTotalAmountDiscount(\Magento\Framework\DB\Select $select)
    {

        return $this->getConnection()->fetchOne(
            $select->from(
                $this->getTable('sales_order'),
                ['return' => new \Zend_Db_Expr('SUM(base_discount_amount * base_to_global_rate)')]
            )
        );
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     * @param \Magento\Framework\DB\Select $selectSegment
     * @param bool                         $currentPeriod
     *
     * @return float|int|string
     */
    public function getTotalAmountCost(
        \Magento\Framework\DB\Select $select,
        \Magento\Framework\DB\Select $selectSegment,
        $currentPeriod = false
    ) {

        $pandaSalesTable = $this->getTable('panda_sales_extra_costs');

        if ($currentPeriod) {
            return $this->getConnection()->fetchOne(
                $select->from(
                    $this->getTable('sales_order'),
                    [
                        'return' => new \Zend_Db_Expr(
                            "SUM(base_total_invoiced_cost * base_to_global_rate + (select SUM(investment) " .
                            "from `$pandaSalesTable`))"
                        ),
                    ]
                )
            );
        }

        $period = $this->scopeConfig->getValue(
            'panda_equity/equity/period',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
        if ($period == 'm') {
            $interval = 'MONTH';
        } else {
            $interval = 'YEAR';
        }

        $selectSegment->from(
            $this->getTable('sales_order'),
            [
                'return' => new \Zend_Db_Expr(
                    "COUNT(*)"
                ),
            ]
        )
                      ->where('panda_acquisition_campaign IS NOT NULL')
                      ->where(
                          "created_at >= DATE( ? - INTERVAL 1 $interval)",
                          $this->pandaHelper->gmtDateTime()
                      );

        $totalOrdersSegment = $this->getConnection()->fetchOne($selectSegment);

        $selectTotal = clone $select;
        $selectTotal->from(
            $this->getTable('panda_sales_extra_costs'),
            [
                'return' => new \Zend_Db_Expr(
                    "SUM(investment)"
                ),
            ]
        )
                    ->where('panda_acquisition_campaign IS NOT NULL');

        $totalOrdersGlobal = $this->getConnection()->fetchOne($selectTotal);

        $selectSales = $this->getConnection()->select();
        $selectSales->from($this->getTable('sales_order'))
                    ->where(
                        "from_date <=DATE( ? - INTERVAL 1 $interval)",
                        $this->pandaHelper->gmtDate()
                    );

        $totalCost = $this->getConnection()->fetchOne($selectSales);

        return $totalCost * $totalOrdersSegment / $totalOrdersGlobal;
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     *
     * @return string
     */
    public function getRetentionRate(\Magento\Framework\DB\Select $select)
    {

        $selectOld = clone $select;

        $period = $this->scopeConfig->getValue(
            'panda_equity/equity/period',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );

        if ($period == 'm') {
            $interval = 'MONTH';
        } else {
            $interval = 'YEAR';
        }

        $selectOld->from($this->getTable('sales_order'), ['customer_email']);
        $selectOld->distinct(true);

        $selectOld->where(
            "created_at >= DATE( ? - INTERVAL 2 $interval)",
            $this->pandaHelper->gmtDateTime()
        );

        $selectOld->where(
            "created_at <= DATE( ? - INTERVAL 1 $interval)",
            $this->pandaHelper->gmtDateTime()
        );

        $startCustomers = $this->getConnection()->fetchCol($selectOld);
        $startCustomers = array_filter($startCustomers);

        $select->from($this->getTable('sales_order'), ['customer_email']);
        $select->distinct(true);

        $select->where(
            "created_at >= DATE( ? - INTERVAL 1 $interval)",
            $this->pandaHelper->gmtDateTime()
        );

        $select->where('customer_email NOT IN (?)', $startCustomers);

        $resultN = $this->getConnection()->fetchCol($select);
        $finalCustomers = array_filter($resultN);

        if (count($finalCustomers) == 0) {
            return 0;
        }

        return round(count($finalCustomers) * 100 / count($startCustomers));
    }

    /**
     * @param $table
     *
     * @return string
     */
    public function getTable($table)
    {

        return $this->getResource()->getTable($table);
    }

    /**
     * @param                                   $formulaParse
     * @param \Magento\Framework\DB\Select|null $selectSegments
     *
     * @return mixed
     */
    public function parseFormula($formulaParse, \Magento\Framework\DB\Select $selectSegments = null)
    {

        $formulaParse = preg_replace('/\s/', '', $formulaParse);
        $global = [];

        $period = $this->scopeConfig->getValue(
            'panda_equity/equity/period',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );

        $initialParse = [
            'global_orders_number',
            'global_orders_completed',
            'global_orders_average',
            'global_orders_number_average',
            'global_invoiced_amount',
            'global_shipping_amount',
            'global_profit_amount',
            'global_taxes_amount',
            'global_orders_amount',
            'global_cost_amount',
            'global_refunded_amount',
            'retention_rate',
            'customer_lifespan',
            'rate_of_discount',
        ];

        foreach ($initialParse as $formula) {
            # {global_orders_number}
            # {global_orders_number[2011]}
            # {global_orders_number[n-1]}
            while (preg_match(
                "/\{$formula(\[((=|>|<=|>=|<)?((19|20)\d{2})|n([-<>])\d|n|([-<>=]{1,2})\d{1,3})\])?\}/",
                $formulaParse,
                $periodToDismantle
            )
            ) {
                if (!isset($global[$formula])) {
                    $year = null;
                    $days = null;
                    $month = null;
                    $periodValue = null;
                    $operator = null;
                    $currentPeriod = false;

                    if (count($periodToDismantle) > 1) {
                        if (count($periodToDismantle) > 3) {
                            $operator = array_pop($periodToDismantle);
                        }

                        $formula = str_replace($periodToDismantle[1], '', $formula);

                        // {global_orders_number[n]}
                        if (isset($periodToDismantle[2][0]) &&
                            $periodToDismantle[2][0] == 'n') {
                            $currentPeriod = true;

                            // {global_orders_number[122]}
                        } elseif (count($periodToDismantle) == 3 &&
                                  isset($periodToDismantle[2]) &&
                                  is_numeric($periodToDismantle[2])) {
                            $days = (int) $periodToDismantle[2];
                            $operator = '=';

                            // {global_orders_number[n>9]}
                            // {global_orders_number[n-9]}
                            // {global_orders_number[n<9]}
                        } elseif (stripos($periodToDismantle[2], 'n') !== false) {
                            $tmpPeriodValue = explode($operator, $periodToDismantle[2]);
                            $periodValue = $tmpPeriodValue[1];

                            // {global_orders_number[>=2019]}
                            // {global_orders_number[=2019]}
                            // {global_orders_number[<=2019]}
                        } elseif (isset($periodToDismantle[4]) &&
                                  strlen($periodToDismantle[4]) == 4 &&
                                  isset($periodToDismantle[5]) &&
                                  strlen($periodToDismantle[5]) == 2) {
                            $year = $periodToDismantle[4];
                            $operator = $periodToDismantle[3];

                            // {global_orders_number[<=121]}
                            // {global_orders_number[=121]}
                            // {global_orders_number[>=1221]}
                        } elseif (substr($periodToDismantle[2], 0, strlen($operator)) == $operator) {
                            $days = (int) $periodToDismantle[2];
                        }
                    }

                    if ($operator == '-') {
                        $operator = '=';
                    }

                    /** @var \Magento\Framework\DB\Select $select */
                    $select = $this->getConnection()->select();

                    if (stripos($formula, 'orders_number') === false) {
                        $select->where('sales_order.state IN (?)', ['complete', 'closed']);
                    }

                    if ($currentPeriod) {
                        if ($period == 'y') {
                            $select->where("YEAR(created_at) = YEAR() ");
                        }

                        if ($period == 'm') {
                            $select->where("DATE_FORMAT(created_at,'%Y-%m') = DATE_FORMAT(NOW(),'%Y-%m')");
                        }
                    }

                    if ($periodValue && $period == 'y') {
                        $select->where("YEAR(created_at) $operator ? ", $periodValue);
                    }

                    if ($periodValue && $period == 'm') {
                        $periodYear = (new \DateTime())->sub(new \DateInterval('P' . $periodValue . 'M'))
                                                       ->format('Y');

                        $select->where("DATE_FORMAT(created_at,'%Y-%m') $operator ?", $periodYear . '-' . $periodValue);
                    }

                    if ($year) {
                        $select->where("YEAR(created_at) $operator ?", $year);
                    }

                    if ($days) {
                        $select->where("created_at >= DATE( NOW() - INTERVAL ? DAY)", new \Zend_Db_Expr($days));
                    }

                    $selectCost = clone $select;

                    if ($selectSegments) {
                        $select->where($this->getTable('sales_order') . '.customer_email IN (?)', $selectSegments);
                    }

                    switch ($formula) {
                        case "{retention_rate}":
                            $global[$formula] = $this->getRetentionRate($select);
                            break;
                        case "{customer_lifespan}":
                            $global[$formula] = $this->scopeConfig->getValue(
                                'panda_equity/equity/lifespan',
                                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
                            );
                            break;
                        case "{rate_of_discount}":
                            $global[$formula] = $this->scopeConfig->getValue(
                                'panda_equity/equity/rate',
                                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
                            );
                            break;
                        case "{global_orders_average}":
                            $global[$formula] = $this->getTotalOrdersAvg($select);
                            break;
                        case "{global_orders_average_number}":
                            $global[$formula] = $this->getNumberOrdersAvg($select);
                            break;
                        case "{global_orders_number}":
                            $global[$formula] = $this->getTotalOrders($select);
                            break;
                        case "{global_orders_completed}":
                            $global[$formula] = $this->getTotalCompletedOrders($select);
                            break;
                        case "{global_shipping_amount}":
                            $global[$formula] = $this->getTotalAmountShipping($select);
                            break;
                        case "{global_orders_amount}":
                            $global[$formula] = $this->getTotalAmountOrders($select);
                            break;
                        case "{global_refunded_amount}":
                            $global[$formula] = $this->getTotalAmountRefunded($select);
                            break;
                        case "{global_taxes_amount}":
                            $global[$formula] = $this->getTotalAmountTaxes($select);
                            break;
                        case "{global_profit_amount}":
                            $global[$formula] = $this->getTotalAmountProfit($select, $selectCost, $currentPeriod);
                            break;
                        case "{global_discount_amount}":
                            $global[$formula] = $this->getTotalAmountDiscount($select);
                            break;
                        case "{global_cost_amount}":
                            $global[$formula] = $this->getTotalAmountCost($select, $selectCost, $currentPeriod);
                            break;
                        default:
                            $global[$formula] = '';
                            break;
                    }
                }

                $formulaParse = str_replace($formula, $global[$formula], $formulaParse);
            }
        }

        return $formulaParse;
    }

    /**
     * @param bool   $validate
     * @param string $customerEmail
     *
     * @return array
     */
    public function run($validate = false, $customerEmail = '')
    {

        $connection = $this->getResource()->getConnection();

        $customerKipsTable = $this->getTable('panda_customers_kpis');
        $customerSegmentsRecordsTable = $this->getTable('panda_segments_records');
        $customerSegmentsTable = $this->getTable('panda_segments');

        $segments = $this->segmentsFactory->create()->getCollection();

        $fields = array_keys(self::ALLOWED_FIELDS);
        $fields = array_combine($fields, $fields);

        foreach ($fields as $key => $field) {
            $fields[$key] = '{' . $field . '}';
        }

        $iStartedTransaction = false;

        if (!$validate && $connection->getTransactionLevel() == 0) {
            $iStartedTransaction = true;
            $connection->beginTransaction();
        }

        if (!$validate && !$customerEmail) {
            foreach ($segments as $segment) {
                $selectRecords = $connection->select();
                $selectRecords->from($customerSegmentsRecordsTable, ['email'])
                              ->where('segment_id=?', $segment->getId());

                $results = $connection->fetchRow(
                    $connection->select()
                               ->from($customerKipsTable, self::ALLOWED_FIELDS)
                               ->where('email_meta IN(?)', $selectRecords)
                );

                $connection->update($customerSegmentsTable, $results, ['segment_id=?' => $segment->getId()]);
            }
        }

        foreach ($this->getCollection() as $formulas) {
            try {
                $segFormula = [];
                $data = [];
                $dataFormula = [];
                for ($i = 0; $i <= 5; $i++) {
                    $formula = $formulas->getData('formula_' . $i);

                    $formulaCustomer = $this->parseFormula($formula);

                    $data['formula_' . $i] = str_replace($fields, array_keys($fields), $formulaCustomer);

                    $dataFormula['formula_' . $i . '_result'] = '(' . str_replace(
                            $fields,
                            self::ALLOWED_FIELDS,
                            $formulaCustomer
                        ) . ')';

                    if ($data['formula_' . $i] == $formulas->getData('formula_' . $i) ||
                        trim($data['formula_' . $i]) == ''
                    ) {
                        unset($data['formula_' . $i]);
                    } elseif ($i > 0) {
                        $data['formula_' . $i] = new \Zend_Db_Expr(
                            'CAST(' . $data['formula_' . $i] . ' AS DECIMAL(12,4)) '
                        );
                    }

                    if ($i == 0 && isset($data['formula_' . $i])) {
                        $data['loyal'] = new \Zend_Db_Expr('IF(' . $data['formula_' . $i] . ',1,0) ');
                        unset($data['formula_' . $i]);
                    }

                    if (!$customerEmail) {
                        foreach ($segments as $segment) {
                            $select = $this->getConnection()->select()
                                           ->from($customerSegmentsRecordsTable, 'email')
                                           ->where('segment_id=?', $segment->getId());

                            $select2 = $this->getConnection()->select()
                                            ->from($customerKipsTable, self::ALLOWED_FIELDS)
                                            ->where('email_meta IN (?)', $select);

                            $result = $this->getConnection()->fetchRow($select2);

                            if (count(array_filter($result)) == 0) {
                                continue;
                            }

                            $formulasegments = $this->parseFormula($formula, $select);

                            $resultReplace = [];
                            foreach ($result as $key => $item) {
                                $resultReplace['{' . $key . '}'] = $item;
                            }

                            $segFormula[$segment->getId()]['formula_' . $i] = str_replace(
                                array_keys($resultReplace),
                                $resultReplace,
                                $formulasegments
                            );

                            if ($segFormula[$segment->getId()]['formula_' . $i] == $formulas->getData('formula_' . $i) ||
                                trim($segFormula[$segment->getId()]['formula_' . $i]) == ''
                            ) {
                                unset($segFormula[$segment->getId()]['formula_' . $i]);
                            } else {
                                $segFormula[$segment->getId()]['formula_' . $i] = new \Zend_Db_Expr(
                                    'CAST(' . $segFormula[$segment->getId()]['formula_' . $i] . ' AS DECIMAL(12,4)) '
                                );
                            }
                        }
                    }
                }

                if (!$validate && $data) {
                    $updateCondition = [
                        'store_id_meta IN (?)' => $this->getStoreIdsForWebsite($formulas->getData('website_id')),
                    ];

                    if ($customerEmail) {
                        $updateCondition['email_meta=?'] = $customerEmail;
                    }

                    $connection->update(
                        $customerKipsTable,
                        $data,
                        $updateCondition
                    );

                    if (isset($segFormula) && is_array($segFormula) && !$customerEmail) {
                        foreach ($segFormula as $segId => $seg) {
                            unset($seg['formula_0']);

                            $connection->update(
                                $customerSegmentsTable,
                                $seg,
                                [
                                    'segment_id=?' => $segId,
                                ]
                            );
                        }
                    }

                    if (!$customerEmail) {
                        unset($dataFormula['formula_0_result']);
                        $dataFormula = array_filter($dataFormula);

                        foreach ($dataFormula as $key => $value) {
                            if ($value == '()') {
                                unset($dataFormula[$key]);
                            }
                        }

                        $result = $connection->fetchRow(
                            $connection->select()
                                       ->from($customerKipsTable, $dataFormula)
                        );

                        $connection->update($this->getFormulas()->getResource()->getMainTable(), $result);

                        $this->getFormulas()
                             ->setData('cron_last_run', $this->pandaHelper->gmtDate())
                             ->save();
                    }

                    if ($iStartedTransaction) {
                        $connection->commit();
                    }
                } else {
                    return ['kpis' => $data, 'segments' => $segFormula];
                }
            } catch (\Exception $e) {
                if (!$validate) {
                    if ($iStartedTransaction) {
                        $connection->rollBack();
                    }

                    $this->pandaHelper->logException($e);
                }
            }
        }

        return true;
    }

    /**
     * @param $table
     * @param $bind
     *
     * @return string
     */
    public function buildUpdateStatement($table, $bind)
    {

        if (!$bind) {
            return false;
        }

        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
        $connection = $this->getResource()->getConnection();
        $set = [];
        $i = 0;
        foreach ($bind as $col => $val) {
            if ($val instanceof \Zend_Db_Expr) {
                $val = $val->__toString();
                unset($bind[$col]);
            } else {
                if ($connection->supportsParameters('positional')) {
                    $val = '?';
                } else {
                    if ($connection->supportsParameters('named')) {
                        unset($bind[$col]);
                        $bind[':col' . $i] = $val;
                        $val = ':col' . $i;
                        $i++;
                    }
                }
            }
            $set[] = $connection->quoteIdentifier($col, true) . ' = ' . $val;
        }

        return "UPDATE " . $connection->quoteIdentifier($table, true) . ' SET ' . implode(', ', $set);
    }

    /**
     * @return $this
     * @throws \Exception
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function afterSave()
    {

        $connection = $this->getResource()->getConnection();

        $data = $this->run(true);
        $customerKipsTable = $this->getTable('panda_customers_kpis');
        $customerSegmentsTable = $this->getTable('panda_segments');

        if (is_array($data)) {
            $i = 1;
            foreach ($data as $key => $sql) {
                try {
                    if ($key == 'kpis') {
                        $sqlUpdate = $this->buildUpdateStatement($this->getTable($customerKipsTable), $sql);
                        if ($sqlUpdate) {
                            $connection->fetchRow('EXPLAIN ' . $sqlUpdate . ' LIMIT 1');
                        }
                    }
                    if ($key == 'segments') {
                        $sqlUpdate = $this->buildUpdateStatement($this->getTable($customerSegmentsTable), reset($sql));
                        if ($sqlUpdate) {
                            $connection->fetchRow('EXPLAIN ' . $sqlUpdate . ' LIMIT 1');
                        }
                    }
                } catch (\Exception $e) {
                    $this->pandaHelper->logException($e);
                    throw new \Magento\Framework\Exception\CouldNotSaveException(
                        __(
                            'Could not save Formula %1. Please check the Variables format. ' .
                            'Additional info logged to the panda.log file',
                            $i
                        )
                    );
                }
                $i++;
            }
        }

        return parent::afterSave();
    }

    /**
     *
     */
    public function cron()
    {

        $date = new \Zend_Date();

        $formulas = $this->getCollection()->addFieldToFilter('cron', ['neq' => '0']);

        /** @var self $formula */
        foreach ($formulas as $formula) {
            if ($formula->getCron() == 'd' ||
                ($formula->getCron() == 'w' && $date->get('e') == 1) ||
                ($formula->getCron() == 'm' && $date->get('d') == 1)
            ) {
                $this->load($formula->getId())
                     ->run();
                $formula->setData('cron_last_run', $this->pandaHelper->gmtDateTime())
                        ->save();
            }
        }
    }

    /**
     * Get formula id
     *
     * @return string
     */
    public function getFormulaId()
    {

        return $this->getData(self::FORMULA_ID);
    }

    /**
     * Set formula id
     *
     * @param string $formula_id
     *
     * @return Formulas
     */
    public function setFormulaId($formula_id)
    {

        return $this->setData(self::FORMULA_ID, $formula_id);
    }

    /**
     * @param $cron
     *
     * @return $this
     */
    public function setCron($cron)
    {

        return $this->setData('cron', $cron);
    }

    /**
     * @param $cronLastRun
     *
     * @return $this
     */
    public function setCronLastRun($cronLastRun)
    {

        return $this->setData('cron_last_run', $cronLastRun);
    }

    /**
     * Get formula 1
     *
     * @return string
     */
    public function getFormula1()
    {

        return $this->getData(self::FORMULA_1);
    }

    /**
     * Set formula 1
     *
     * @param string $formula1
     *
     * @return Formulas
     */
    public function setFormula1($formula1)
    {

        return $this->setData(self::FORMULA_1, $formula1);
    }

    /**
     * Get formula 1 name
     *
     * @return string
     */
    public function getFormula1Name()
    {

        return $this->getData(self::FORMULA_1_NAME);
    }

    /**
     * Set formula 1 name
     *
     * @param $formula1Name
     *
     * @return Formulas
     */
    public function setFormula1Name($formula1Name)
    {

        return $this->setData(self::FORMULA_1_NAME, $formula1Name);
    }

    /**
     * Get formula 2
     *
     * @return string
     */
    public function getFormula2()
    {

        return $this->getData(self::FORMULA_2);
    }

    /**
     * Set formula 2
     *
     * @param string $formula2
     *
     * @return Formulas
     */
    public function setFormula2($formula2)
    {

        return $this->setData(self::FORMULA_2, $formula2);
    }

    /**
     * Get formula 2 name
     *
     * @return string
     */
    public function getFormula2Name()
    {

        return $this->getData(self::FORMULA_2_NAME);
    }

    /**
     * @param string $formula2Name
     *
     * @return $this|FormulasInterface
     */
    public function setFormula2Name($formula2Name)
    {

        return $this->setData(self::FORMULA_2_NAME, $formula2Name);
    }

    /**
     * Get formula 3
     *
     * @return string
     */
    public function getFormula3()
    {

        return $this->getData(self::FORMULA_3);
    }

    /**
     * Set formula 3
     *
     * @param string $formula3
     *
     * @return Formulas
     */
    public function setFormula3($formula3)
    {

        return $this->setData(self::FORMULA_3, $formula3);
    }

    /**
     * Get formula 3 name
     *
     * @return string
     */
    public function getFormula3Name()
    {

        return $this->getData(self::FORMULA_3_NAME);
    }

    /**
     * Set formula 3 name
     *
     * @param $formula3Name
     *
     * @return Formulas
     */
    public function setFormula3Name($formula3Name)
    {

        return $this->setData(self::FORMULA_3_NAME, $formula3Name);
    }

    /**
     * Get formula 4
     *
     * @return string
     */
    public function getFormula4()
    {

        return $this->getData(self::FORMULA_4);
    }

    /**
     * Set formula 4
     *
     * @param string $formula4
     *
     * @return Formulas
     */
    public function setFormula4($formula4)
    {

        return $this->setData(self::FORMULA_4, $formula4);
    }

    /**
     * Get formula 4 name
     *
     * @return string
     */
    public function getFormula4Name()
    {

        return $this->getData(self::FORMULA_4_NAME);
    }

    /**
     * Set formula 4 name
     *
     * @param $formula4Name
     *
     * @return Formulas
     */
    public function setFormula4Name($formula4Name)
    {

        return $this->setData(self::FORMULA_4_NAME, $formula4Name);
    }

    /**
     * Get formula 5
     *
     * @return string
     */
    public function getFormula5()
    {

        return $this->getData(self::FORMULA_5);
    }

    /**
     * Set formula 5
     *
     * @param string $formula5
     *
     * @return Formulas
     */
    public function setFormula5($formula5)
    {

        return $this->setData(self::FORMULA_5, $formula5);
    }

    /**
     * Get formula 5 name
     *
     * @return string
     */
    public function getFormula5Name()
    {

        return $this->getData(self::FORMULA_5_NAME);
    }

    /**
     * Set formula 5 name
     *
     * @param $formula5Name
     *
     * @return Formulas
     */
    public function setFormula5Name($formula5Name)
    {

        return $this->setData(self::FORMULA_5_NAME, $formula5Name);
    }

    /**
     * Get formula 6
     *
     * @return string
     */
    public function getFormula6()
    {

        return $this->getData(self::FORMULA_6);
    }

    /**
     * Set formula 6
     *
     * @param string $formula6
     *
     * @return Formulas
     */
    public function setFormula6($formula6)
    {

        return $this->setData(self::FORMULA_6, $formula6);
    }

    /**
     * Get formula 6 name
     *
     * @return string
     */
    public function getFormula6Name()
    {

        return $this->getData(self::FORMULA_6_NAME);
    }

    /**
     * Set formula 6 name
     *
     * @param $formula6Name
     *
     * @return Formulas
     */
    public function setFormula6Name($formula6Name)
    {

        return $this->setData(self::FORMULA_6_NAME, $formula6Name);
    }

    /**
     * Get formula 7
     *
     * @return string
     */
    public function getFormula7()
    {

        return $this->getData(self::FORMULA_7);
    }

    /**
     * Set formula 7
     *
     * @param string $formula7
     *
     * @return Formulas
     */
    public function setFormula7($formula7)
    {

        return $this->setData(self::FORMULA_7, $formula7);
    }

    /**
     * Get formula 7 name
     *
     * @return string
     */
    public function getFormula7Name()
    {

        return $this->getData(self::FORMULA_7_NAME);
    }

    /**
     * Set formula 7 name
     *
     * @param $formula7Name
     *
     * @return Formulas
     */
    public function setFormula7Name($formula7Name)
    {

        return $this->setData(self::FORMULA_7_NAME, $formula7Name);
    }

    /**
     * Get formula 8
     *
     * @return string
     */
    public function getFormula8()
    {

        return $this->getData(self::FORMULA_8);
    }

    /**
     * Set formula 8
     *
     * @param string $formula8
     *
     * @return Formulas
     */
    public function setFormula8($formula8)
    {

        return $this->setData(self::FORMULA_8, $formula8);
    }

    /**
     * Get formula 8 name
     *
     * @return string
     */
    public function getFormula8Name()
    {

        return $this->getData(self::FORMULA_8_NAME);
    }

    /**
     * Set formula 8 name
     *
     * @param $formula8Name
     *
     * @return Formulas
     */
    public function setFormula8Name($formula8Name)
    {

        return $this->setData(self::FORMULA_8_NAME, $formula8Name);
    }

    /**
     * Get formula 9
     *
     * @return string
     */
    public function getFormula9()
    {

        return $this->getData(self::FORMULA_9);
    }

    /**
     * Set formula 9
     *
     * @param string $formula9
     *
     * @return Formulas
     */
    public function setFormula9($formula9)
    {

        return $this->setData(self::FORMULA_9, $formula9);
    }

    /**
     * Get formula 9 name
     *
     * @return string
     */
    public function getFormula9Name()
    {

        return $this->getData(self::FORMULA_9_NAME);
    }

    /**
     * Set formula 9 name
     *
     * @param $formula9Name
     *
     * @return Formulas
     */
    public function setFormula9Name($formula9Name)
    {

        return $this->setData(self::FORMULA_9_NAME, $formula9Name);
    }

    /**
     * Get formula 10
     *
     * @return string
     */
    public function getFormula10()
    {

        return $this->getData(self::FORMULA_10);
    }

    /**
     * Set formula 10
     *
     * @param string $formula10
     *
     * @return Formulas
     */
    public function setFormula10($formula10)
    {

        return $this->setData(self::FORMULA_10, $formula10);
    }

    /**
     * Get formula 10 name
     *
     * @return string
     */
    public function getFormula10Name()
    {

        return $this->getData(self::FORMULA_10_NAME);
    }

    /**
     * Set formula 10 name
     *
     * @param $formula10Name
     *
     * @return Formulas
     */
    public function setFormula10Name($formula10Name)
    {

        return $this->setData(self::FORMULA_10_NAME, $formula10Name);
    }

    /**
     * @return mixed
     */
    public function getCron()
    {

        return $this->getData('cron');
    }

    /**
     * @return mixed
     */
    public function getCronLastRun()
    {

        return $this->getData('cron_last_run');
    }

    /**
     * @param $storeId
     *
     * @return mixed
     */
    public function getStoreIdsForWebsite($storeId)
    {

        $websiteId = $this->storeManager->getStore($storeId)
                                        ->getWebsiteId();

        return $this->storeManager->getWebsite($websiteId)
                                  ->getStoreIds();
    }
}
