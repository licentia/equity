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

use Licentia\Reports\Model\Indexer;

/**
 * Class Metadata
 *
 * @package Licentia\Equity\Model
 */
class Metadata
{

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected \Magento\Framework\Filesystem $filesystem;

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected \Licentia\Equity\Helper\Data $pandaHelper;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection;

    /**
     * @var \Magento\Reports\Model\ResourceModel\Order\CollectionFactory
     */
    protected \Magento\Reports\Model\ResourceModel\Order\CollectionFactory $reportsOrderCollection;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected \Magento\Customer\Model\CustomerFactory $customerFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollection;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected \Magento\Catalog\Model\ProductFactory $productFactory;

    /**
     * @var \Magento\Reports\Model\ResourceModel\Event\CollectionFactory
     */
    protected \Magento\Reports\Model\ResourceModel\Event\CollectionFactory $reportsEventCollection;

    /**
     * @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory
     */
    protected \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollection;

    /**
     * @var SegmentsFactory
     */
    protected SegmentsFactory $segmentsFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected \Licentia\Panda\Helper\Data $logger;

    /**
     * @var
     */
    protected $salesAverage = null;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone;

    /**
     * @var ResourceModel\SegmentsFactory
     */
    protected ResourceModel\SegmentsFactory $segmentsResourceFactory;

    /**
     * @var KpisFactory
     */
    protected KpisFactory $kpisFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected \Magento\Store\Model\StoreManagerInterface $storeManager;

    /**
     * @var FormulasFactory
     */
    protected FormulasFactory $formulasFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\OptionFactory
     */
    protected \Magento\Eav\Model\Entity\Attribute\OptionFactory $eavOptionFactory;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory
     */
    protected \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollection;

    /**
     * @var \Magento\Framework\Registry
     */
    protected \Magento\Framework\Registry $registry;

    /**
     * @var Indexer
     */
    protected Indexer $indexer;

    /**
     * Metadata constructor.
     *
     * @param \Licentia\Reports\Model\IndexerFactory                           $indexer
     * @param FormulasFactory                                                  $formulasFactory
     * @param \Magento\Store\Model\StoreManagerInterface                       $storeManagerInterface
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface             $timezone
     * @param \Licentia\Equity\Helper\Data                                     $pandaHelper
     * @param KpisFactory                                                      $kpisFactory
     * @param SegmentsFactory                                                  $segmentsFactory
     * @param \Licentia\Panda\Helper\Data                                      $logger
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory       $orderCollection
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollection
     * @param \Magento\Customer\Model\CustomerFactory                          $customerFactory
     * @param \Magento\Catalog\Model\ProductFactory                            $productFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory       $quoteCollection
     * @param \Magento\Reports\Model\ResourceModel\Order\CollectionFactory     $reportsOrderCollection
     * @param \Magento\Reports\Model\ResourceModel\Event\CollectionFactory     $reportsEventCollection
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory     $reviewCollection
     * @param \Magento\Framework\App\Config\ScopeConfigInterface               $scopeInterface
     * @param \Magento\Eav\Model\Entity\Attribute\OptionFactory                $eavOptionFactory
     * @param \Magento\Framework\Filesystem                                    $filesystem
     * @param \Magento\Framework\Registry                                      $registry
     * @param ResourceModel\SegmentsFactory                                    $segmentsResourceFactory
     */
    public function __construct(
        \Licentia\Reports\Model\IndexerFactory $indexer,
        FormulasFactory $formulasFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Licentia\Equity\Helper\Data $pandaHelper,
        KpisFactory $kpisFactory,
        SegmentsFactory $segmentsFactory,
        \Licentia\Panda\Helper\Data $logger,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollection,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollection,
        \Magento\Reports\Model\ResourceModel\Order\CollectionFactory $reportsOrderCollection,
        \Magento\Reports\Model\ResourceModel\Event\CollectionFactory $reportsEventCollection,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface,
        \Magento\Eav\Model\Entity\Attribute\OptionFactory $eavOptionFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Registry $registry,
        \Licentia\Equity\Model\ResourceModel\SegmentsFactory $segmentsResourceFactory
    ) {

        $this->indexer = $indexer->create();
        $this->formulasFactory = $formulasFactory;
        $this->storeManager = $storeManagerInterface;
        $this->kpisFactory = $kpisFactory;
        $this->registry = $registry;
        $this->pandaHelper = $pandaHelper;
        $this->segmentsResourceFactory = $segmentsResourceFactory;
        $this->orderCollection = $orderCollection;
        $this->quoteCollection = $quoteCollection;
        $this->customerCollection = $customerCollection;
        $this->customerFactory = $customerFactory;
        $this->reportsOrderCollection = $reportsOrderCollection;
        $this->productFactory = $productFactory;
        $this->segmentsFactory = $segmentsFactory;
        $this->reviewCollection = $reviewCollection;
        $this->reportsEventCollection = $reportsEventCollection;
        $this->scopeConfig = $scopeInterface;
        $this->filesystem = $filesystem;
        $this->logger = $logger;
        $this->eavOptionFactory = $eavOptionFactory;
        $this->timezone = $timezone;
    }

    /**
     * @param null $customerId
     * @param null $table
     * @param null $email
     * @param null $oldEmail
     * @param null $storeId
     *
     * @return array|bool
     * @throws \Exception
     */
    public function initActivityRow($customerId = null, $table = null, $email = null, $oldEmail = null, $storeId = null)
    {

        if (!$customerId && !$email && !$this->registry->registry('panda_customer_activities_rebuild')) {
            $customerId = $this->pandaHelper->getCustomerId();
        }

        if (!$oldEmail) {
            $oldEmail = $email;
        }

        if ($customerId && $email !== $oldEmail) {
            return $this->rebuildCustomerActivities($customerId, $email, $oldEmail);
        }

        $websiteId = $this->storeManager->getStore($storeId)
                                        ->getWebsiteId();
        if ($customerId) {
            $email = $this->getConnection()
                          ->fetchOne(
                              $this->getConnection()
                                   ->select()
                                   ->from($this->getTable('customer_entity'), ['email'])
                                   ->where('entity_id=?', $customerId)
                          );
        }

        $email = strtolower($email);

        $result = false;

        if ($customerId) {
            $select = $this->getConnection()
                           ->select()
                           ->from($this->getTable($table))
                           ->where('customer_id=?', $customerId);

            $result = $this->getConnection()->fetchRow($select);
        }

        if (!$result) {
            $select = $this->getConnection()
                           ->select()
                           ->from($this->getTable($table))
                           ->where('email_meta=?', $email)
                           ->where('website_id=? OR website_id IS NULL', $websiteId);

            $result = $this->getConnection()->fetchRow($select);

            if (!$result) {
                $insert = [];
                $insert['email_meta'] = $email;
                $insert['website_id'] = $websiteId;

                if ($customerId) {
                    $insert['customer_id'] = $customerId;
                }

                if (empty($insert['email_meta']) && empty($insert['customer_id'])) {
                    return false;
                }

                try {
                    $this->getConnection()->insert($this->getTable($table), $insert);
                } catch (\Exception $e) {
                    $this->pandaHelper->logException($e);
                }
                $select = $this->getConnection()
                               ->select()
                               ->from($this->getTable($table))
                               ->where('website_id=?', $websiteId)
                               ->where('email_meta=?', $email);

                $result = $this->getConnection()->fetchRow($select);
            }
        }

        return $result;
    }

    /**
     * Get connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    public function getConnection()
    {

        return $this->segmentsResourceFactory->create()->getConnection();
    }

    /**
     * @param string $table
     *
     * @return mixed
     */
    public function getTable($table = 'panda_customers_kpis')
    {

        if (null === $table) {
            $table = 'panda_customers_kpis';
        }

        /** @var \Licentia\Equity\Model\ResourceModel\Segments $resource */
        $resource = $this->segmentsResourceFactory->create();

        return $resource->getTable($table);
    }

    /**
     * @param      $event
     * @param bool $entity
     *
     * @return bool|int
     * @throws \Exception
     */
    public function quoteRelated($event, $entity = false)
    {

        if ($entity) {
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $event;
        } else {
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $event->getDataObject();
        }

        if (!$quote->getCustomerId() && !$quote->getCustomerEmail()) {
            return false;
        }

        $customerId = null;
        if ($quote->getCustomerId()) {
            $customerId = $quote->getCustomerId();
        } else {
            $otherQuotes = $this->quoteCollection
                ->create()
                ->addFieldToFilter('customer_email', ['eq' => $quote->getCustomerEmail()])
                ->addFieldToFilter(
                    'store_id',
                    [
                        'in' => $this->getStoreIdsForWebsite($quote->getStoreId()),
                    ]
                )
                ->addFieldToFilter('customer_id', ['notnull' => true]);
            if ($otherQuotes && $otherQuotes->getFirstItem() &&
                $otherQuotes->getFirstItem()
                            ->getCustomerId()
            ) {
                $customerId = $otherQuotes->getFirstItem()->getCustomerId();
            }
        }

        $result = $this->initActivityRow($customerId, null, $quote->getCustomerEmail(), null, $quote->getStoreId());
        if (!$result) {
            return false;
        }

        $updateData = [];

        if ($quote->getIsActive() != 1 || $quote->getItemsCount() == 0) {
            $updateData['abandoned'] = null;
            $updateData['abandoned_date'] = null;
            $updateData['cart_totals'] = null;
            $updateData['cart_number'] = null;
            $updateData['cart_products'] = null;
        } else {
            $lastDay = new \DateTime();
            $firstDay = new \DateTime(substr($quote->getUpdatedAt(), 0, 10) . ' 00:00:00');

            $updateData['abandoned'] = $lastDay->diff($firstDay)
                                               ->format('%a');
            $updateData['abandoned_date'] = $quote->getUpdatedAt();
            $updateData['cart_totals'] = $quote->getBaseGrandTotal();
            $updateData['cart_number'] = $quote->getItemsCount();
            $updateData['cart_products'] = $quote->getItemsQty();
        }

        if ($customerId) {
            $field = 'customer_id';
            $value = $customerId;
        } else {
            $field = 'email_meta';
            $value = $quote->getCustomerEmail();
        }

        $updateData['store_id_meta'] = $quote->getStoreId();

        $this->updateSegments($customerId, 'quote');

        return $this->getConnection()->update($this->getTable(), $updateData, [$field . '=?' => $value]);
    }

    /**
     * @param      $event
     * @param bool $entity
     *
     * @return int
     * @throws \Exception
     */
    public function orderRelated($event, $entity = false)
    {

        if ($entity) {
            /** @var  \Magento\Sales\Model\Order $order */
            $order = $event;
        } else {
            /** @var  \Magento\Sales\Model\Order $order */
            $order = $event->getDataObject();
        }

        //????
        if (!$order->getCustomerId() && !$order->getCustomerEmail()) {
            return false;
        }

        $email = $order->getCustomerEmail();
        $customerId = null;
        $field = 'email_meta';
        $value = $order->getCustomerEmail();

        if ($order->getCustomerId()) {
            $customerId = $order->getCustomerId();
            $customerEmails = $this->getCustomerEmails($customerId);
        } else {
            $customerEmails[] = $order->getCustomerEmail();
        }

        $newStatus = $order->getData('status');
        $olderStatus = $order->getOrigData('status');

        if ($newStatus == $olderStatus && $entity === false) {
            return false;
        }

        $result = $this->initActivityRow($customerId, null, $email, null, $order->getStoreId());

        if (!$result) {
            return false;
        }

        $updateData = [];

        $select = $this->getConnection()->select();
        $select->from($this->getTable('sales_order'), ['count' => new \Zend_Db_Expr('COUNT(*)'),]);
        $select->where('customer_email IN (?)', $customerEmails);

        $updateData['number_orders'] = $this->getConnection()->fetchOne($select);
        $updateData['last_activity_date'] = $this->pandaHelper->gmtDate();

        if ($newStatus == 'pending_payment') {
            $updateData['pending_payment'] = 0;
            $updateData['pending_payment_date'] = $this->pandaHelper->gmtDate();
        }
        if ($olderStatus == 'pending_payment') {
            $updateData['pending_payment'] = null;
        }

        if (($order->getState() == 'complete' and
             $order->getOrigData('state') && $order->getOrigData('state') != 'complete') ||
            ($entity && ($order->getState() == 'complete' || $order->getState() == 'closed'))
        ) {
            $select = $this->getConnection()->select();
            $select->from(
                $this->getTable('sales_order'),
                [
                    'base_lifetime' => new \Zend_Db_Expr('SUM(base_grand_total * base_to_global_rate)'),
                    'base_avgsale'  => new \Zend_Db_Expr('AVG(base_grand_total * base_to_global_rate)'),
                    'num_orders'    => new \Zend_Db_Expr('COUNT(base_grand_total)'),
                ]
            );
            $select->where('customer_email IN (?)', $customerEmails);
            $select->where('state =?', 'complete');

            $customerTotals = $this->getConnection()->fetchRow($select);

            $updateData['number_completed_orders'] = $customerTotals['num_orders'];
            $updateData['order_amount'] = $customerTotals['base_lifetime'];
            $updateData['order_average'] = $customerTotals['base_avgsale'];

            $lastDay = new \DateTime();

            if (!$result['first_order']) {
                //No first order recorded
                $updateData['first_order'] = 0;

                // Let's load it
                $select = $this->getConnection()->select();
                $select->from($this->getTable('sales_order'), ['created_at'])
                       ->where('customer_email IN (?)', $customerEmails)
                       ->where('store_id IN (?)', $this->getStoreIdsForWebsite($order->getStoreId()))
                       ->where('state =?', 'complete');
                $firstOrderDate = $this->getConnection()->fetchOne($select);

                if (!$firstOrderDate) {
                    $firstOrderDate = $this->pandaHelper->gmtDate('Y-m-d');
                }

                $firstDay = new \DateTime(substr($firstOrderDate, 0, 10) . ' 00:00:00');

                $updateData['first_order'] = $lastDay->diff($firstDay)
                                                     ->format('%a');
                $updateData['first_order_date'] = $firstOrderDate;

                $result['first_order'] = $updateData['first_order'];
                $result['first_order_date'] = $updateData['first_order_date'];
            }

            // Last order date is the current order
            $lastOrderDate = new \DateTime(substr($order->getCreatedAt(), 0, 10) . ' 00:00:00');
            $updateData['last_order'] = $lastDay->diff($lastOrderDate)
                                                ->format('%a');
            $updateData['last_order_date'] = $order->getCreatedAt();

            $updateData['subtotal'] = $result['subtotal'] + $order->getBaseSubtotal();
            $updateData['shipping'] = $result['shipping'] + $order->getBaseShippingAmount();
            $updateData['taxes'] = $result['taxes'] + $order->getBaseTaxAmount();
            $updateData['discount'] = $result['discount'] + $order->getBaseDiscountAmount();
            $updateData['refunded'] = $result['refunded'] + $order->getBaseTotalRefunded();
            $updateData['cost'] = $result['cost'] + $order->getBaseTotalInvoicedCost() +
                                  $order->getData('panda_shipping_cost');

            $updateData['profit'] = $result['profit'] + (($order->getData('base_total_paid') -
                                                          $order->getData('base_total_refunded') -
                                                          $order->getData('base_tax_invoiced') -
                                                          $order->getData('base_shipping_invoiced') -
                                                          $order->getData('base_total_invoiced_cost') -
                                                          $order->getData('panda_shipping_cost')) *
                                                         $order->getData('base_to_global_rate'));

            if ($order->getBaseDiscountAmount() > 0) {
                $updateData['number_orders_with_discount'] = $result['number_orders_with_discount'] + 1;
            }

            // The number os days between the first and current order
            $orderDays = $this->pandaHelper->getDaysBetweenDates($result['first_order_date'], $order->getCreatedAt());

            // Average days between orders
            $orderDaysAverage = $orderDays / $updateData['number_orders'];
            $updateData['order_average_days'] = $orderDaysAverage;

            $selectUncompletedOrders = $this->getConnection()->select();
            $selectUncompletedOrders->from($this->getTable('sales_order'), ['COUNT' => new \Zend_Db_Expr('COUNT(*)')])
                                    ->where('customer_email IN (?)', $customerEmails)
                                    ->where('store_id IN (?)', $this->getStoreIdsForWebsite($order->getStoreId()))
                                    ->where('state NOT IN (?)', ['complete', 'closed']);
            $unCompleteOrders = $this->getConnection()->fetchOne($selectUncompletedOrders);

            $totalOrders = (int) $updateData['number_orders'] + (int) $unCompleteOrders;

            $updateData['percentage_complete_orders'] = round($totalOrders * 100 / $updateData['number_orders']);

            $currentDate = new \DateTime($this->pandaHelper->gmtDate());
            $currentDate->sub(new \DateInterval('P1Y'));
            $real = $currentDate->format('Y-m-d H:i:s');

            $select = $this->getConnection()->select();
            $select->from(
                $this->getTable('sales_order'),
                [
                    'created_at',
                    'order_average_1year' => new \Zend_Db_Expr('SUM(base_grand_total * base_to_global_rate)'),
                ]
            )
                   ->where('customer_email IN (?)', $customerEmails)
                   ->where('store_id IN (?)', $this->getStoreIdsForWebsite($order->getStoreId()))
                   ->where('created_at >=?', $real)
                   ->where('state =?', 'complete')
                   ->order('created_at ASC');

            $ordersAmountLastYearResult = $this->getConnection()->fetchRow($select);
            $ordersAmountLastYear = $ordersAmountLastYearResult['order_average_1year'];

            if ($ordersAmountLastYear == 0) {
                $updateData['order_average_1year'] = 0;
            } else {
                $orderDays = $this->pandaHelper->getDaysBetweenDates(
                    $ordersAmountLastYearResult['created_at'],
                    $order->getCreatedAt()
                );

                if ($orderDays > 0) {
                    $updateData['order_average_1year'] = $ordersAmountLastYear / $orderDays;
                } else {
                    $updateData['order_average_1year'] = $ordersAmountLastYear;
                }
            }

            $updateData['order_amount_1year'] = $ordersAmountLastYear;

            $select = $this->getConnection()->select();
            $select->from(
                $this->getTable('sales_order'),
                [
                    'order_average_year' => new \Zend_Db_Expr('base_grand_total * base_to_global_rate'),
                ]
            )
                   ->where('customer_email IN (?)', $customerEmails)
                   ->where('store_id IN (?)', $this->getStoreIdsForWebsite($order->getStoreId()))
                   ->where('created_at <=?', $real)
                   ->where('state =?', 'complete');

            $ordersAmountPreviousYearResult = $this->getConnection()->fetchCol($select);
            $ordersAmountPreviousYear = 0;
            foreach ($ordersAmountPreviousYearResult as $orderTotal) {
                $ordersAmountPreviousYear += $orderTotal;
            }

            $firstOrderDate = false;
            if ($ordersAmountPreviousYear == 0) {
                $updateData['order_average_older'] = 0;
            } else {
                if ($result['first_order_date']) {
                    $firstOrderDate = $result['first_order_date'];
                } else {
                    if ($updateData['first_order_date']) {
                        $firstOrderDate = $updateData['first_order_date'];
                    }
                }
                if ($firstOrderDate) {
                    $lastDay = new \DateTime($currentDate->format('Y-m-d') . ' 00:00:00');
                    $firstDay = new \DateTime($firstOrderDate);
                    $interval = $lastDay->diff($firstDay);

                    if ($interval->days > 0) {
                        $updateData['order_average_older'] = $ordersAmountPreviousYear / $interval->days;
                    } else {
                        $updateData['order_average_older'] = $ordersAmountPreviousYear;
                    }
                }
            }
            $updateData['order_amount_older'] = $ordersAmountPreviousYear;

            $salesAverage = $this->getSalesAverage();

            if ($salesAverage > 0) {
                $updateData['percentage_order_amount'] = round(100 * $updateData['order_average'] / $salesAverage);
            }

            $items = $order->getItemsCollection();

            if (count($items)) {

                /** @var \Magento\Sales\Model\Order\Item $item */
                foreach ($items as $item) {

                    /** @var \Magento\Catalog\Model\Product $product */
                    $product = $this->productFactory->create()->load($item->getProductId());

                    if (!$product || !$product->getId()) {
                        continue;
                    }
                    if ($customerId) {
                        $this->handleAttributes(
                            $product,
                            $customerId,
                            $order->getCustomerEmail(),
                            true,
                            $order->getCreatedAt()
                        );
                    } else {
                        $this->handleAttributes(
                            $product,
                            $order->getCustomerId(),
                            $order->getCustomerEmail(),
                            true,
                            $order->getCreatedAt()
                        );
                    }
                }
            }
        } else {
            $select = $this->getConnection()->select();
            $select->from($this->getTable('sales_order'), ['num_orders' => new \Zend_Db_Expr('COUNT(*)')])
                   ->where('customer_email IN (?)', $customerEmails)
                   ->where('store_id IN (?)', $this->getStoreIdsForWebsite($order->getStoreId()))
                   ->where('state =?', 'complete');

            $totalOrders = $this->getConnection()->fetchOne($select);

            if ($totalOrders == 0) {
                $updateData['percentage_complete_orders'] = 0;
            } else {
                $updateData['percentage_complete_orders'] = round($totalOrders * 100 / $updateData['number_orders']);
            }
        }

        $updateData['store_id_meta'] = $order->getStoreId();

        $this->updateSegments($order->getCustomerId(), 'order');

        return $this->getConnection()->update($this->getTable(), $updateData, [$field . '=?' => $value]);
    }

    /**
     *
     */
    public function updatePercentageOrderAmount()
    {

        $salesAverage = $this->getSalesAverage();

        $updateData = [];
        $updateData['percentage_order_amount'] = new \Zend_Db_Expr(
            ' ROUND(100 * order_average / ' . $salesAverage . ' ) '
        );

        $this->getConnection()->update($this->getTable(), $updateData);
    }

    /**
     * @throws \Exception
     */
    public function updateOldDate()
    {

        $selectDate = new \DateTime($this->pandaHelper->gmtDate());
        $selectDate->sub(new \DateInterval('P1Y'));
        $date = $selectDate->format('Y-m-d');

        $connection = $this->getConnection();
        $table = $this->getTable();

        $collection =
            $connection->fetchAll(
                "SELECT customer_id, email_meta, first_order_date FROM  $table WHERE last_order_date>=?",
                $date
            );

        foreach ($collection as $item) {
            $customerId = $item['customer_id'];
            if ($customerId) {
                $customerEmails = $this->getCustomerEmails($customerId);
            } else {
                $customerEmails[] = $item['email_meta'];
            }

            $updateData = [];

            $last1year = $this->orderCollection->create()
                                               ->addFieldToSelect('base_grand_total')
                                               ->addFieldToFilter('state', 'complete')
                                               ->addFieldToFilter('customer_email', ['in' => $customerEmails])
                                               ->addFieldToFilter('created_at', ['gteq' => $date . ' 00:00:00']);

            $recentAmount = 0;
            /** @var \Magento\Sales\Model\Order $month */
            foreach ($last1year as $month) {
                $recentAmount += $month->getData('base_grand_total');
            }

            if ($last1year->count() == 0) {
                $updateData['order_average_1year'] = 0;
            } else {
                $updateData['order_average_1year'] = $recentAmount / 6;
            }
            $updateData['order_amount_1year'] = $recentAmount;

            $previousMonths = $this->orderCollection->create()
                                                    ->addFieldToSelect('base_grand_total')
                                                    ->addFieldToFilter('state', 'complete')
                                                    ->addFieldToFilter('customer_email', ['in' => $customerEmails])
                                                    ->addFieldToFilter('created_at', ['lteq' => $date . ' 00:00:00']);

            $olderAmount = 0;
            foreach ($previousMonths as $month) {
                $olderAmount += $month->getData('base_grand_total');
            }

            if ($previousMonths->count() == 0) {
                $updateData['order_average_older'] = 0;
            } else {
                if ($item['first_order_date']) {
                    $lastDay = new \DateTime($date . ' 00:00:00');
                    $firstDay = new \DateTime($item['first_order_date'] . ' 00:00:00');
                    $interval = $lastDay->diff($firstDay);
                    if (($interval->y * 12 + $interval->m) > 0) {
                        $updateData['order_average_older'] = $olderAmount / ($interval->y * 12 + $interval->m);
                    } else {
                        $updateData['order_average_older'] = $olderAmount;
                    }
                }
            }
            $updateData['order_amount_older'] = $olderAmount;

            $this->getConnection()
                 ->update(
                     $this->getTable(),
                     $updateData,
                     ['email_meta=?' => $item['email_meta']]
                 );
        }
    }

    /**
     * @param      $event
     * @param bool $entity
     *
     * @return bool|int
     * @throws \Exception
     */
    public function accountRelated($event, $entity = false)
    {

        $oldEmail = null;

        /** @var \Magento\Customer\Model\Customer $account */

        if ($entity) {
            $account = $this->customerFactory->create()->load($event->getId());
        } else {
            $account = $event->getDataObject();
            $oldEmail = $account->getOrigData('email');

            if ($account->getOrigData() && !$account->getOrigData('id')) {
                $select = $this->getConnection()
                               ->select()
                               ->from($this->getTable())
                               ->where(
                                   'website_id =?',
                                   $this->storeManager->getStore($account->getStoreId())
                                                      ->getWebsiteId()
                               )
                               ->where('email_meta = ?', $account->getEmail());

                $_result = $this->getConnection()->fetchRow($select);

                if ($_result) {
                    $this->getConnection()
                         ->update(
                             $this->getTable(),
                             ['customer_id' => $account->getId()],
                             ['email_meta=?' => $account->getEmail()]
                         );
                }
                /*
                    if ($_result['email_meta'] != $account->getEmail()) {
                        $oldEmail = $_result['email_meta'];
                        $this->getConnection()->update(
                            $this->getTable(),
                            ['email_meta' => $account->getEmail()],
                            ['customer_id=?' => $account->getId()]
                        );
                    }
                */
            }
        }

        $result = $this->initActivityRow(
            $account->getId(),
            null,
            $account->getEmail(),
            $oldEmail,
            $account->getStoreId()
        );

        if (!$result) {
            return false;
        }

        $updateData = [];

        if ($account->getCreatedAt()) {
            $firstDay = new \DateTime(substr($account->getCreatedAt(), 0, 10) . ' 00:00:00');
            $lastDay = new \DateTime();
            $days = $lastDay->diff($firstDay)
                            ->format('%a');
            $updateData['account'] = $days;
            $updateData['account_date'] = $account->getCreatedAt();
        }

        $dob = $account->getDob();

        if ($dob) {
            $updateData['dob'] = $dob;

            $datetime1 = new \DateTime(date('Y', strtotime('now +1 year')) . substr($dob, 4, 6));
            $datetime2 = new \DateTime(date('Y-m-d'));
            $interval = $datetime2->diff($datetime1);
            $days = $interval->format('%a');

            if ($days > 365) {
                $days -= 365;
            }

            $updateData['anniversary'] = $days;

            $firstDay = new \DateTime($dob);
            $lastDay = new \DateTime();
            $years = $lastDay->diff($firstDay)
                             ->format('%y');

            if ($years > 0) {
                $updateData['age'] = $years;
            }
        }

        $gender = '';
        if ($account->getGender() == 1) {
            $gender = 'male';
        }
        if ($account->getGender() == 2) {
            $gender = 'female';
        }

        $updateData['email_meta'] = $account->getEmail();
        $updateData['store_id_meta'] = $account->getStoreId();
        $updateData['customer_id'] = $account->getId();
        $updateData['customer_name'] = $account->getFirstname() . ' ' . $account->getLastname();
        $updateData['gender'] = $gender;

        $this->updateSegments($account->getId(), 'account');

        return $this->getConnection()->update($this->getTable(), $updateData, ['customer_id=?' => $account->getId()]);
    }

    /**
     * @internal param $result
     */
    public function activityRelated()
    {

        $this->initActivityRow();
    }

    /**
     * @param            $event
     * @param bool|false $entity
     *
     * @return bool
     * @throws \Exception
     */
    public function reviewRelated($event, $entity = false)
    {

        /** @var \Magento\Review\Model\Review $review */
        if ($entity) {
            $review = $event;
        } else {
            $review = $event->getDataObject();
        }

        if (!$review->getCustomerId() || $review->getStatusId() != 1) {
            return false;
        }

        $result = $this->initActivityRow($review->getCustomerId(), null, null, null, $review->getStoreId());

        if ($result['last_review_id'] == $review->getId()) {
            return false;
        }

        $firstDay = new \DateTime(substr($review->getCreatedAt(), 0, 10) . ' 00:00:00');
        $lastDay = new \DateTime();

        $updateData = [];
        $updateData['number_reviews'] = $result['number_reviews'] + 1;
        $updateData['last_review'] = $lastDay->diff($firstDay)
                                             ->format('%a');
        $updateData['last_review_id'] = $review->getId();
        $updateData['last_review_date'] = $review->getCreatedAt();

        $this->updateSegments($review->getCustomerId(), 'review');

        return $this->getConnection()
                    ->update(
                        $this->getTable(),
                        $updateData,
                        ['customer_id=?' => $review->getCustomerId()]
                    );
    }

    /**
     * @param            $email
     * @param            $customerId
     * @param            $string
     *
     * @return int
     * @throws \Exception
     */
    public function searchRelated($email, $customerId, $string)
    {

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = $this->pandaHelper->getCustomerEmail();
        }

        $data = [];
        $data['email'] = $email;
        $data['customer_id'] = $customerId;
        $data['query'] = $string;
        $data['created_at'] = $this->pandaHelper->gmtDate();

        $table = $this->getTable('panda_segments_metadata_searches');

        $this->updateSegments($email, 'search');

        return $this->getConnection()->insert($table, $data);
    }

    /**
     * @param bool $entity
     * @param null $customerIdReport
     * @param null $storeId
     * @param null $visit
     *
     * @return bool
     * @throws \Exception
     */
    public function productRelated($entity = false, $customerIdReport = null, $storeId = null, $visit = null)
    {

        if ($customerIdReport) {
            $customerId = $customerIdReport;
        } else {
            $customerId = $this->pandaHelper->getCustomerId();
        }

        if (!$customerId) {
            return false;
        }

        $this->activityRelated();

        if ($entity && is_numeric($entity)) {
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $this->productFactory->create()
                                            ->setStoreId($storeId)
                                            ->load($entity);
        } else {
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $this->registry->registry('current_product');
        }

        if (!is_object($product) || !$product->getId()) {
            return false;
        }

        $productsTable = 'panda_segments_metadata_products';
        $categoriesTable = 'panda_segments_metadata_categories';

        if ($visit) {
            $visit_at = $visit;
        } else {
            $visit_at = $this->pandaHelper->gmtDate();
        }

        /*
        $select = $this->getConnection()->select()
                       ->from($this->getTable($productsTable))
                       ->where('product_id=?', $product->getId())
                       ->where('customer_id=?', $customerId);

        $result = $this->getConnection()->fetchRow($select);

        if (!$result) {

        */
        $this->getConnection()
             ->insert(
                 $this->getTable($productsTable),
                 [
                     'customer_id' => $customerId,
                     'sku'         => $product->getSku(),
                     'visited_at'  => $visit_at,
                     'product_id'  => $product->getId(),
                     'views'       => 1,
                 ]
             );

        /*
        } else {
            $this->getConnection()->update(
                $this->getTable($productsTable),
                [
                    'visited_at' => $visit_at,
                    'views'      => $result['views'] + 1,
                ],
                [
                    'customer_id=?' => $customerId,
                    'product_id=?'  => $product->getId(),
                ]
            );
        }
        */

        //Category
        $cat = $this->registry->registry('current_category');
        if ($cat && $cat->getId()) {
            $selectCat = $this->getConnection()
                              ->select()
                              ->from($this->getTable($categoriesTable))
                              ->where('category_id=?', $cat->getId())
                              ->where('customer_id=?', $customerId);

            $resultCat = $this->getConnection()->fetchRow($selectCat);

            if (!$resultCat) {
                $this->getConnection()
                     ->insert(
                         $this->getTable($categoriesTable),
                         [
                             'customer_id' => $customerId,
                             'visited_at'  => $this->pandaHelper->gmtDate(),
                             'category_id' => $cat->getId(),
                             'views'       => 1,
                         ]
                     );
            } else {
                $this->getConnection()
                     ->update(
                         $this->getTable($categoriesTable),
                         [
                             'visited_at' => $this->pandaHelper->gmtDate(),
                             'views'      => $resultCat['views'] + 1,
                         ],
                         ['customer_id=?' => $customerId, 'category_id=?' => $cat->getId()]
                     );
            }
        }

        $this->updateSegments($customerId, 'product');

        $this->handleAttributes($product, $customerId, null, false, $visit_at);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param                                $customerId
     * @param                                $customerEmail
     * @param bool                           $order
     * @param bool                           $date
     */
    protected function handleAttributes(
        \Magento\Catalog\Model\Product $product,
        $customerId,
        $customerEmail,
        $order = false,
        $date = false
    ) {

        $attrsTable = 'panda_segments_metadata_attrs';
        $productsTable = 'panda_segments_metadata_products';

        $fieldUpdate = ($order) ? 'bought' : 'views';

        $field = 'customer_id';
        $value = $customerId;

        if ($order) {
            if ($customerId) {
                $field = 'customer_id';
                $value = $customerId;
            } else {
                $field = 'email';
                $value = $customerEmail;
            }
        }

        if (!$date) {
            $date = $this->pandaHelper->gmtDate();
        }
        try {
            if ($order && $customerId) {
                $this->getConnection()
                     ->insert(
                         $this->getTable($productsTable),
                         [
                             'bought'      => 1,
                             'bought_at'   => $date,
                             'product_id'  => $product->getId(),
                             'customer_id' => $customerId,
                             'sku'         => $product->getSku(),
                         ]
                     );
            }

            $logAttributes = $this->scopeConfig->getValue(
                'panda_magna/segments/attributes',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            );
            $logAttributes = explode(',', $logAttributes);

            $attributes = $product->getAttributes();

            foreach ($attributes as $attribute) {
                if (in_array($attribute->getData('attribute_code'), $logAttributes) &&
                    $attribute->getId() && $attribute->getData('attribute_code') != 'price') {
                    $optionId = $product->getData($attribute->getName());

                    if (!$optionId) {
                        continue;
                    }

                    $validAttribute = $this->getConnection()
                                           ->select()
                                           ->from($this->getTable('eav_attribute_option'))
                                           ->where('attribute_id=?', $attribute->getId())
                                           ->where('option_id=?', $optionId);

                    if ($this->getConnection()->fetchRow($validAttribute)) {
                        $select = $this->getConnection()
                                       ->select()
                                       ->from($this->getTable($attrsTable))
                                       ->where('attribute_id=?', $attribute->getId())
                                       ->where('option_id=?', $optionId)
                                       ->where($field . '=?', $value);

                        $result = $this->getConnection()->fetchRow($select);

                        if (!$result) {
                            $this->getConnection()
                                 ->insert(
                                     $this->getTable($attrsTable),
                                     [
                                         $field                 => $value,
                                         $fieldUpdate . '_date' => $date,
                                         'option_id'            => $optionId,
                                         'attribute_id'         => $attribute->getId(),
                                         $fieldUpdate           => 1,
                                     ]
                                 );
                        } else {
                            $this->getConnection()
                                 ->update(
                                     $this->getTable($attrsTable),
                                     [
                                         $fieldUpdate           => $result[$fieldUpdate] + 1,
                                         $fieldUpdate . '_date' => $date,
                                     ],
                                     [
                                         $field . '=?'    => $value,
                                         'option_id=?'    => $optionId,
                                         'attribute_id=?' => $attribute->getId(),
                                     ]
                                 );
                        }
                    }
                }
            }
        } catch (\Exception $e) {
        }

        unset($product);
    }

    /**
     *
     */
    public function dailyCron()
    {

        $salesAverage = $this->getSalesAverage();

        $updateData = [];
        $updateData['number_visits'] = new \Zend_Db_Expr(
            "IF(DATE_FORMAT(now(),'%Y-%m-%d')=number_visits,number_visits,number_visits+1)"
        );
        $updateData['abandoned'] = new \Zend_Db_Expr("datediff(now(),abandoned_date) ");
        $updateData['last_order'] = new \Zend_Db_Expr("datediff(now(),last_order_date)");
        $updateData['first_order'] = new \Zend_Db_Expr("datediff(now(),first_order_date)");
        $updateData['last_review'] = new \Zend_Db_Expr("datediff(now(),last_review_date)");
        $updateData['account'] = new \Zend_Db_Expr("datediff(now(),account_date)");
        $updateData['last_activity'] = new \Zend_Db_Expr("datediff(now(),last_activity_date)");
        $updateData['pending_payment'] = new \Zend_Db_Expr("datediff(now(),pending_payment_date)");
        $updateData['percentage_order_amount'] = new \Zend_Db_Expr("100 * order_average / {$salesAverage}");
        $updateData['percentage_orders_with_discount'] = new \Zend_Db_Expr(
            "ABS(number_completed_orders * 100 / number_orders_with_discount)"
        );
        $updateData['anniversary'] = new \Zend_Db_Expr(
            "ABS(IF(RIGHT(CURDATE(),5) >= RIGHT(dob,5), DATEDIFF(CURDATE(),CONCAT(YEAR(CURDATE()+ INTERVAL 1 YEAR)," .
            "RIGHT(dob,6))) , DATEDIFF(CONCAT(YEAR(CURDATE()),RIGHT(dob,6)),CURDATE())))"
        );
        $updateData['age'] = new \Zend_Db_Expr("TIMESTAMPDIFF(YEAR, dob, CURDATE())");

        $this->getConnection()->update($this->getTable(), $updateData);
    }

    /**
     * @return bool|Metadata
     */
    public function reindexEquity()
    {

        return $this->rebuildCustomerMetadata(false, null, null, true);
    }

    /**
     * @param null $output
     * @param null $customerId
     * @param bool $first
     *
     * @throws \Exception
     */
    public function rebuildCustomerMetadataCommandLine($output = null, $customerId = null, $first = false)
    {

        if (!$this->indexer->canReindex('equity')) {
            throw new \RuntimeException("Indexer status does not allow reindexing");
        }

        $this->indexer->updateIndexStatus(Indexer::STATUS_WORKING, 'equity');

        if ($first) {
            $this->rebuild(false, $customerId);
        }

        $this->rebuildCustomerMetadata(true, $output, $customerId, $first);

        $this->indexer->updateIndexStatus(Indexer::STATUS_VALID, 'equity');
    }

    /**
     * @param bool $commandLine
     * @param null $output
     * @param null $customerId
     * @param bool $first
     *
     * @return $this|bool
     */
    public function rebuildCustomerMetadata($commandLine = false, $output = null, $customerId = null, $first = false)
    {

        $baseMemory = memory_get_usage();

        define('PANDA_CRON', true);

        $indexer = $this->indexer->load('equity');

        if (!$commandLine) {
            $number = (int) $indexer['last_entity_id'];
            $type = $indexer['entity_type'];

            $processNumber = (int) $this->scopeConfig->getValue('panda_magna/segments/process');
            if ($processNumber == 0) {
                $processNumber = 200;
            }
        } else {
            $number = 0;
            $processNumber = 1000;
            $type = '';
        }

        if ($customerId) {
            $customer = $this->customerFactory->create()->load($customerId);
            if (!$customer->getId()) {
                return false;
            }
            # $this->_kpisFactory->create()
            #                    ->load($customer->getEmail(), 'email_meta')
            #                    ->delete();
        }

        if (($type == 'order' || $commandLine) && !$customerId) {
            $page = 1;
            do {
                $orders = $this->orderCollection->create()
                                                ->addFieldToFilter('customer_id', ['null' => null])
                                                ->setOrder('entity_id', 'ASC')
                                                ->addFieldToFilter('entity_id', ['gt' => $number])
                                                ->setPage($page, $processNumber);

                $orders->getSelect()
                       ->reset('group')
                       ->reset('columns')
                       ->columns(['*']);

                $customerLabelSelect = $this->getConnection()
                                            ->select()->from($this->getTable('customer_entity'), ['email']);

                $orders->getSelect()->where('customer_email NOT IN (?)', $customerLabelSelect);

                $lastId = 0;
                /** @var \Magento\Sales\Model\Order $order */
                foreach ($orders as $order) {
                    $this->orderRelated($order, true);
                    $lastId = $order->getId();

                    if ($commandLine) {
                        $output->write("Equity | Order ID: " . $order->getId() . "\r");
                    } else {
                        $this->indexer->updateIndex('order', $lastId, 'equity');
                    }
                }

                if (!$commandLine) {
                    $this->pandaHelper->scheduleEvent('panda_build_metadata');

                    if ($orders->count() == $processNumber) {
                        $this->indexer->updateIndex('order', $lastId, 'equity');

                        return false;
                    } else {
                        $this->indexer->updateIndex('quote', 0, 'equity');

                        return false;
                    }
                }

                $page++;
            } while ($orders->count() == $processNumber);
        }

        unset($orders);
        if (($type == 'quote' || $commandLine) && !$customerId) {
            $page = 1;
            do {
                $quotes = $this->quoteCollection->create()
                                                ->addFieldToFilter('customer_id', ['null' => null])
                                                ->setOrder('entity_id', 'ASC')
                                                ->addFieldToFilter('is_active', 1)
                                                ->addFieldToFilter('items_count', ['neq' => '0'])
                                                ->addFieldToFilter('entity_id', ['gt' => $number])
                                                ->setPageSize($processNumber)
                                                ->setCurPage($page);

                $customerLabelSelect = $this->getConnection()
                                            ->select()->from($this->getTable('customer_entity'), ['email']);

                $quotes->getSelect()->where('customer_email NOT IN (?)', $customerLabelSelect);
                $lastId = 0;
                foreach ($quotes as $quote) {
                    $this->quoteRelated($quote, true);
                    $lastId = $quote->getId();

                    if ($commandLine) {
                        $output->write("Equity | Quote ID: " . $quote->getId() . "\r");
                    } else {
                        $this->indexer->updateIndex('quote', $lastId, 'equity');
                    }
                }

                if (!$commandLine) {
                    $this->pandaHelper->scheduleEvent('panda_build_metadata');

                    if ($quotes->count() == $processNumber) {
                        $this->indexer->updateIndex('quote', $lastId, 'equity');

                        return false;
                    } else {
                        $this->indexer->updateIndex('customer', 0, 'equity');

                        return false;
                    }
                }

                $page++;
            } while ($quotes->count() == $processNumber);
        }
        unset($quotes);

        $page = 1;
        $lastCustomerId = 0;
        do {
            if ($commandLine) {
                $customers = $this->customerCollection->create()
                                                      ->addAttributeToSort('entity_id', 'ASC')
                                                      ->setPageSize($processNumber);
                if ($customerId) {
                    $customers->addAttributeToFilter('entity_id', $customerId);
                } else {
                    $customers->addAttributeToFilter('entity_id', ['gt' => (int) $lastCustomerId]);
                }

                if ($first) {
                    $customers->getSelect()
                              ->where(
                                  'entity_id NOT IN (select customer_id from ' .
                                  $this->getTable() . ' WHERE  customer_id IS NOT NULL) '
                              );
                }
            } else {
                $customers = $this->customerCollection->create()
                                                      ->addAttributeToSort('entity_id', 'ASC')
                                                      ->setPage($page, $processNumber);

                if ($customerId) {
                    $customers->addAttributeToFilter('entity_id', $customerId);
                } else {
                    $customers->addAttributeToFilter('entity_id', ['gt' => $number]);
                }
            }

            /** @var \Magento\Customer\Model\Customer $customer */
            foreach ($customers as $customer) {
                try {
                    $this->accountRelated($customer, true);

                    $orders = $this->orderCollection->create();

                    $orders->getSelect()
                           ->where(
                               "customer_id= " . $customer->getId() . " OR customer_email IN (?)",
                               $this->getCustomerEmails($customer->getId())
                           );

                    foreach ($orders as $order) {
                        $this->orderRelated($order, true);
                    }

                    $products = $this->reportsEventCollection->create()
                                                             ->addFieldToFilter('event_type_id', 1)
                                                             ->addFieldToFilter('subject_id', $customer->getId());

                    /** @var \Magento\Reports\Model\Event $product */
                    foreach ($products as $product) {
                        $this->productRelated(
                            $product->getObjectId(),
                            $customer->getId(),
                            $product->getStoreId(),
                            $product->getLoggedAt()
                        );
                    }

                    $quotes = $this->quoteCollection->create()
                                                    ->addFieldToFilter('customer_id', $customer->getId())
                                                    ->addFieldToFilter('is_active', 1);

                    foreach ($quotes as $quote) {
                        $this->quoteRelated($quote, true);
                    }

                    $reviews = $this->reviewCollection->create()
                                                      ->addStatusFilter(1)
                                                      ->addCustomerFilter($customer->getId());

                    foreach ($reviews as $review) {
                        $this->reviewRelated($review, true);
                    }
                } catch (\Exception $e) {
                    $this->pandaHelper->logException($e);
                }

                $lastCustomerId = $customer->getId();

                if ($commandLine) {
                    $output->write(
                        "Equity | Customer ID: " . $lastCustomerId . ' | Memory Usage: ' .
                        round((memory_get_usage() - $baseMemory) / 1024 / 1024) . ' Mb' . "\r"
                    );
                } else {
                    $this->indexer->updateIndex('customer', $lastCustomerId, 'equity');
                }
            }

            if ($customers->count() == $processNumber && !$commandLine) {
                $this->pandaHelper->scheduleEvent('panda_build_metadata');

                $this->indexer->updateIndex('customer', $lastCustomerId, 'equity');
            } elseif (!$commandLine) {
                $result = $this->initActivityRow($customer->getId(), null, null, null, $customer->getStoreId());
                if ($result) {
                    $where = [];
                    if ($customerId) {
                        $where = ['customer_id = ?' => $customerId];
                    }

                    $this->getConnection()
                         ->update(
                             $this->getTable(),
                             [
                                 'last_activity' => new \Zend_Db_Expr("datediff(now(),last_activity_date)"),
                             ],
                             $where
                         );
                }
            }
            if (!$commandLine) {
                break;
            }

            $page++;
        } while ($customers->count() == $processNumber);

        unset($customers);

        return $this;
    }

    /**
     * @param bool $schedule
     * @param bool $customerId
     *
     * @throws \Exception
     */
    public function rebuild($schedule = true, $customerId = false)
    {

        if (!$customerId) {
            $this->indexer->updateIndex('order', 0, 'equity');
        }

        if ($schedule == true) {
            $this->pandaHelper->scheduleEvent('panda_build_metadata');
        }

        $where = [];
        if ($customerId) {
            $where = ['customer_id=?' => $customerId];
        }

        $this->getConnection()->delete($this->getTable('panda_customers_kpis'), $where);
        $this->getConnection()->delete($this->getTable('panda_segments_metadata_attrs'), $where);
        #$this->getConnection()->delete($this->getTable('panda_segments_metadata_categories'));
        $this->getConnection()->delete($this->getTable('panda_segments_metadata_products'), $where);
    }

    /**
     * @return mixed
     */
    protected function getSalesAverage()
    {

        if (!$this->salesAverage) {
            $select = $this->getConnection()->select();
            $select->from(
                $this->getTable('sales_order'),
                [
                    'base_avgsale' => new \Zend_Db_Expr('AVG(base_grand_total * base_to_global_rate)'),
                ]
            )
                   ->where('state =?', 'complete');

            $this->salesAverage = (float) $this->getConnection()->fetchOne($select);
        }

        return $this->salesAverage;
    }

    /**
     * @param $customerId
     *
     * @return array|null
     */
    protected function getCustomerEmails($customerId)
    {

        $customerEmails = [];
        if ($customerId) {
            $allCustomerOrders = $this->orderCollection->create()
                                                       ->addFieldToSelect('customer_email')
                                                       ->addFieldToFilter('customer_id', $customerId);
            $allCustomerOrders->getSelect()->distinct();
            $customerEmails = $allCustomerOrders->getColumnValues('customer_email');
            $_customer = $this->customerFactory->create()->load($customerId);
            if ($_customer) {
                $customerEmails[] = $_customer->getEmail();
            }
        }

        $customerEmails = array_filter($customerEmails);
        $customerEmails = array_unique($customerEmails);

        return $customerEmails;
    }

    /**
     * @param $customerId
     * @param $email
     * @param $oldEmail
     *
     * @return bool
     * @throws \Exception
     */
    public function rebuildCustomerActivities($customerId, $email, $oldEmail)
    {

        $this->registry->register('panda_customer_activities_rebuild', true);

        $connection = $this->getConnection();
        $connection->beginTransaction();
        try {
            $connection->delete($this->getTable('panda_customers_kpis'), ['customer_id' => $customerId]);
            $connection->delete($this->getTable('panda_customers_kpis'), ['email_meta' => $email]);
            $connection->delete($this->getTable('panda_customers_kpis'), ['email_meta' => $oldEmail]);

            $connection->delete($this->getTable('panda_segments_metadata_attrs'), ['customer_id' => $customerId]);
            $connection->delete($this->getTable('panda_segments_metadata_attrs'), ['email' => $email]);
            $connection->delete($this->getTable('panda_segments_metadata_attrs'), ['email' => $oldEmail]);

            $connection->delete($this->getTable('panda_segments_metadata_products'), ['customer_id' => $customerId]);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
        }

        $this->rebuildCustomerMetadata(false, null, $customerId);

        $this->registry->unregister('panda_customer_activities_rebuild');

        return $this->initActivityRow($customerId);
    }

    /**
     * @param $customerId
     * @param $affects
     *
     * @return $this|bool
     * @throws \Exception
     */
    public function updateSegments($customerId, $affects)
    {

        if (defined('PANDA_CRON')) {
            return false;
        }

        if (!$customerId) {
            return false;
        }

        $resource = $this->kpisFactory->create()->getResource();
        $connection = $resource->getConnection();

        $segments = $this->segmentsFactory->create()
                                          ->getCollection()
                                          ->addFieldToFilter('cron', 'r')
                                          ->addFieldToFilter('is_active', 1)
                                          ->addFieldToFilter('affects_' . $affects, '1');

        if ($segments->count() > 0) {

            /** @var Segments $segment */
            foreach ($segments as $segment) {
                if (!$segment->getRealTimeUpdateCron()) {
                    $segment->updateSegmentRecords($customerId);
                } else {
                    $connection->insert(
                        $resource->getTable('panda_segments_update_queue'),
                        [
                            'segment_id'  => $segment->getId(),
                            'customer_id' => $customerId,
                        ]
                    );
                }
            }

            if (!$segment->getRealTimeUpdateCron()) {
                $email = $connection->fetchOne(
                    $connection->select()
                               ->from($resource->getTable('customer_entity'), ['email'])
                               ->where('entity_id=?', $customerId)
                );

                if ($email) {
                    $this->formulasFactory->create()->run(false, $email);
                }
            }

            $this->pandaHelper->getCustomerSegmentsIds($customerId, true);
        }

        return $this;
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
