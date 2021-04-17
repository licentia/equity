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

use Licentia\Equity\Api\Data\SegmentsInterface;
use Licentia\Reports\Model\Indexer;

/**
 * Class Segments
 *
 * @package Licentia\Panda\Model
 */
class Segments extends \Magento\Rule\Model\AbstractModel implements SegmentsInterface
{

    const UPDATE_SEGMENT_REQUEST = [
        ['label' => 'New Order', 'value' => 'order'],
        ['label' => 'Order Complete', 'value' => 'order_complete'],
        ['label' => 'New Invoice', 'value' => 'invoice'],
        ['label' => 'New Account', 'value' => 'account'],
    ];

    /**
     * @var string
     */
    protected string $_eventPrefix = 'panda_segments';

    /**
     * @var string
     */
    protected string $_eventObject = 'rule';

    /**
     * @var
     */
    protected $customersIds;

    /**
     * @var
     */
    protected $customersEmails;

    /**
     * @var
     */
    protected $customersData;

    /**
     * @var Segments\Action\CollectionFactory
     */
    protected Segments\Action\CollectionFactory $collectionFactory;

    /**
     * @var Segments\Condition\CombineFactory
     */
    protected Segments\Condition\CombineFactory $combineFactory;

    /**
     * @var ResourceModel\Segments\CollectionFactory
     */
    protected ResourceModel\Segments\CollectionFactory $segmentsCollection;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected \Magento\Customer\Model\CustomerFactory $customerFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollection;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection;

    /**
     * @var Segments\ListSegmentsFactory
     */
    protected Segments\ListSegmentsFactory $listSegmentsFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected \Magento\Store\Model\StoreManagerInterface $storeManager;

    /**
     * @var ResourceModel\Segments\ListSegments\CollectionFactory
     */
    protected ResourceModel\Segments\ListSegments\CollectionFactory $listSegmentsCollection;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory
     */
    protected \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollection;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    protected \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollection;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected \Magento\Quote\Model\QuoteFactory $quoteFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected \Magento\Sales\Model\OrderFactory $orderFactory;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Iterator
     */
    protected \Magento\Framework\Model\ResourceModel\Iterator $resourceInterator;

    /**
     * @var
     */
    protected $customersGroup;

    /**
     * @var \Licentia\Panda\Model\SubscribersFactory
     */
    protected \Licentia\Panda\Model\SubscribersFactory $subscribersFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone;

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected \Licentia\Equity\Helper\Data $pandaHelper;

    /**
     * @var FormulasFactory
     */
    protected FormulasFactory $formulasFactory;

    /**
     * @var Indexer
     */
    protected Indexer $indexer;

    /**
     * @var false|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var \Magento\Framework\Model\ResourceModel\AbstractResource|\Magento\Framework\Model\ResourceModel\Db\AbstractDb|null
     */
    protected $resource;

    /**
     *
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\Segments::class);
    }

    /**
     * Segments constructor.
     *
     * @param \Licentia\Reports\Model\IndexerFactory                           $indexer
     * @param \Magento\Framework\Model\ResourceModel\Iterator                  $resourceInterator
     * @param \Magento\Quote\Model\QuoteFactory                                $quoteFactory
     * @param \Magento\Sales\Model\OrderFactory                                $orderFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory       $quoteCollection
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory    $groupCollection
     * @param \Magento\Store\Model\StoreManagerInterface                       $store
     * @param \Magento\Customer\Model\CustomerFactory                          $customerFactory
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollection
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory       $orderCollection
     * @param ResourceModel\Segments\CollectionFactory                         $segmentsCollection
     * @param ResourceModel\Segments\ListSegments\CollectionFactory            $listSegmentsCollection
     * @param Segments\Condition\CombineFactory                                $combineFactory
     * @param Segments\ListSegmentsFactory                                     $listSegmentsFactory
     * @param Segments\Action\CollectionFactory                                $collectionFactory
     * @param \Licentia\Panda\Model\SubscribersFactory                         $subscribersFactory
     * @param FormulasFactory                                                  $formulasFactory
     * @param \Magento\Framework\Model\Context                                 $context
     * @param \Licentia\Equity\Helper\Data                                     $pandaHelper
     * @param \Magento\Framework\Registry                                      $registry
     * @param \Magento\Framework\Data\FormFactory                              $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface             $localeDate
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null     $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null               $resourceCollection
     * @param array                                                            $data
     */
    public function __construct(
        \Licentia\Reports\Model\IndexerFactory $indexer,
        \Magento\Framework\Model\ResourceModel\Iterator $resourceInterator,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollection,
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollection,
        \Magento\Store\Model\StoreManagerInterface $store,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollection,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection,
        ResourceModel\Segments\CollectionFactory $segmentsCollection,
        ResourceModel\Segments\ListSegments\CollectionFactory $listSegmentsCollection,
        Segments\Condition\CombineFactory $combineFactory,
        Segments\ListSegmentsFactory $listSegmentsFactory,
        Segments\Action\CollectionFactory $collectionFactory,
        \Licentia\Panda\Model\SubscribersFactory $subscribersFactory,
        FormulasFactory $formulasFactory,
        \Magento\Framework\Model\Context $context,
        \Licentia\Equity\Helper\Data $pandaHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);

        $this->indexer = $indexer->create();
        $this->subscribersFactory = $subscribersFactory;
        $this->segmentsCollection = $segmentsCollection;
        $this->combineFactory = $combineFactory;
        $this->collectionFactory = $collectionFactory;
        $this->customerCollection = $customerCollection;
        $this->orderCollection = $orderCollection;
        $this->customerFactory = $customerFactory;
        $this->storeManager = $store;
        $this->listSegmentsFactory = $listSegmentsFactory;
        $this->listSegmentsCollection = $listSegmentsCollection;
        $this->groupCollection = $groupCollection;
        $this->quoteCollection = $quoteCollection;
        $this->quoteFactory = $quoteFactory;
        $this->orderFactory = $orderFactory;
        $this->resourceInterator = $resourceInterator;
        $this->timezone = $localeDate;
        $this->pandaHelper = $pandaHelper;
        $this->formulasFactory = $formulasFactory;

        $this->resource = $this->getResource();
        $this->connection = $this->resource->getConnection();
    }

    /**
     * @param string $includeNone
     *
     * @return array
     */
    public function getOptionArray($includeNone = 'None')
    {

        $lists = $this->segmentsCollection->create()
                                          ->addFieldToSelect('segment_id')
                                          ->addFieldToSelect('name')
                                          ->addFieldToFilter('is_active', 1)
                                          ->setOrder('name', 'ASC');

        $return = [];

        if ($includeNone) {
            $return[] = ['value' => '0', 'label' => __("-- $includeNone --")];
        }

        /** @var \Licentia\Equity\Model\Segments\ListSegments $list */
        foreach ($lists as $list) {
            $return[] = ['value' => $list->getId(), 'label' => $list->getName()];
        }

        return $return;
    }

    /**
     * @return array
     */
    public function toOptionArrayAutoManaged()
    {

        $lists = $this->segmentsCollection->create()
                                          ->addFieldToSelect('segment_id')
                                          ->addFieldToSelect('name')
                                          ->addFieldToFilter('is_active', 1)
                                          ->addFieldToFilter('manual', 0)
                                          ->setOrder('name', 'ASC');

        $return = [];

        /** @var \Licentia\Equity\Model\Segments\ListSegments $list */
        foreach ($lists as $list) {
            $return[] = ['value' => $list->getId(), 'label' => $list->getName()];
        }

        return $return;
    }

    /**
     * @return Segments\Condition\Combine|\Magento\Rule\Model\Condition\Combine
     */
    public function getConditionsInstance()
    {

        return $this->combineFactory->create();
    }

    /**
     * @return Segments\Action\Collection|\Magento\Rule\Model\Action\Collection
     */
    public function getActionsInstance()
    {

        return $this->collectionFactory->create();
    }

    /**
     * @return array
     */
    public function getStoreIds()
    {

        $websiteIds = explode(',', $this->getData('websites_ids'));

        $storeIds = [];
        foreach ($websiteIds as $websiteId) {
            $storeIds =
                array_merge(
                    $storeIds,
                    $this->storeManager->getWebsite($websiteId)->getStoreIds()
                );
        }

        return $storeIds;
    }

    /**
     * @param null $greaterThanCustomerId
     *
     * @return $this
     */
    public function reindexSegments($greaterThanCustomerId = null)
    {

        $collection = $this->getCollection()->addFieldToFilter('is_active', 1);

        if (!$this->getData('consoleOutput') && !$this->indexer->canReindex('segments')) {
            throw new \RuntimeException("Indexer status does not allow reindexing");
        }

        $this->indexer->updateIndexStatus(Indexer::STATUS_WORKING, 'segments');

        /** @var Segments $segment */
        foreach ($collection as $segment) {
            try {
                $segment->updateSegmentRecords($greaterThanCustomerId);
            } catch (\Exception $e) {
            }
        }

        $this->indexer->updateIndexStatus(Indexer::STATUS_VALID, 'segments');

        return $this;
    }

    /**
     * @param null $customerIdToSegment
     * @param null $greaterThanCustomerId
     *
     * @return array|$this
     */
    public function updateSegmentRecords($customerIdToSegment = null, $greaterThanCustomerId = null)
    {

        $this->_registry->register('panda_segments_customer_id', $customerIdToSegment, true);

        $conditions = $this->getConditions()->asArray();

        if (!isset($conditions['conditions']) || !is_array($conditions['conditions'])) {
            return $this;
        }
        if ($this->getManual() == 1) {
            return $this;
        }

        if ($this->getIsActive() != 1) {
            return $this;
        }

        if (!$customerIdToSegment) {
            $this->indexer->updateIndexStatus(Indexer::STATUS_WORKING, 'segments');
        }

        if (is_numeric($customerIdToSegment)) {
            $field = 'entity_id';
        } else {
            $field = 'panda_customers_kpis.email_meta';
        }

        $write = $this->getResource()->getConnection();
        $table = $write->getTableName('panda_segments_records');

        $params = ['segment_id = ?' => $this->getId(), 'manual=?' => '0'];

        if ($this->getManual() == '0' && $this->getType() == 'customers' && $customerIdToSegment) {
            $params['customer_id = ? '] = $customerIdToSegment;
        }

        $write->update($table, ['revert' => 1], $params);

        $now = $this->pandaHelper->gmtDate();
        $extraData = '';
        $type = '';

        if (is_null($this->customersIds)) {
            $this->setData('run', $now)->save();

            $this->customersIds = [];
            $this->setCollectedAttributes([]);

            $this->_registry->register('panda_segment', $this, true);

            $customerCollection = $this->customerCollection->create();
            $customerCollection->addAttributeToSelect('firstname')
                               ->addAttributeToSelect('lastname');

            if ($customerIdToSegment) {
                $customerCollection->getSelect()->where($field . '=?', $customerIdToSegment);
            }

            $customerCollection->getSelect()
                               ->joinRight(
                                   $customerCollection->getTable('panda_customers_kpis'),
                                   $customerCollection->getTable('panda_customers_kpis') .
                                   '.email_meta = e.email'
                               );

            $customerCollection->getSelect()
                               ->joinLeft(
                                   $customerCollection->getTable('panda_subscribers'),
                                   $customerCollection->getTable('panda_subscribers') . '.email = ' .
                                   $customerCollection->getTable('panda_customers_kpis') . '.email_meta',
                                   [
                                       'cellphone',
                                       'bounces',
                                       'sent',
                                       'views',
                                       'clicks',
                                       'conversions_number',
                                       'conversions_amount',
                                       'conversions_average',
                                       'form_id',
                                       'field_1',
                                       'field_2',
                                       'field_3',
                                       'field_4',
                                       'field_5',
                                       'field_6',
                                       'field_7',
                                       'field_8',
                                       'field_9',
                                       'field_10',
                                       'field_11',
                                       'field_12',
                                       'field_13',
                                       'field_14',
                                       'field_15',
                                   ]
                               );

            $customerCollection->getSelect()->where('store_id_meta IN (?) OR e.store_id IN (?)', $this->getStoreIds());
            $customerCollection->getSelect()->where('e.entity_id IS NOT NULL');

            if ($greaterThanCustomerId) {
                $customerCollection->getSelect()->where('e.entity_id > ?', $greaterThanCustomerId);
            }

            $modelCollection = $this->customerFactory->create();

            $this->resourceInterator->walk(
                $customerCollection->getSelect(),
                [[$this, 'callbackValidateCustomer']],
                [
                    'attributes' => $this->getCollectedAttributes(),
                    'customer'   => $modelCollection,
                ]
            );

            $this->customersEmails = array_unique((array) $this->customersEmails);

            foreach ($this->customersIds as $email => $recordData) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }

                $data = $recordData;

                if (isset($recordData['firstname'], $recordData['lastname'])) {
                    $data['customer_name'] = $recordData['firstname'] . ' ' . $recordData['lastname'];
                }

                $segment = $this->customersData[$email];

                $type = [];
                foreach ($segment as $key => $value) {
                    if (stripos($key, 'type_') !== false) {
                        $type[$key] = $value;
                        unset($segment[$key]);
                    }
                }

                if (count($segment) <= 10) {
                    $extraData = array_keys($segment);
                    $segment = array_values($segment);
                    for ($i = 0; $i < count($segment); $i++) {
                        $data['data_' . ($i + 1)] = $segment[$i];
                    }
                }
                $data['segment_id'] = $this->getId();
                $data['customer_id'] = $recordData['entity_id'];

                if (!isset($data['email'])) {
                    $data['email'] = $recordData['email_meta'];
                }

                if (isset($recordData['subscriber_id'])) {
                    $data['subscriber_id'] = $recordData['subscriber_id'];
                }

                $this->listSegmentsFactory->create()->saveRecord($data);
            }

            if ($customerIdToSegment == null) {
                if (count($this->customersIds) == 0) {
                    $type = '';
                    $extraData = '';
                }

                $i = count($this->customersIds);
                $this->setData('last_update', $now)
                     ->setData('extra_data', json_encode(['type' => $type, 'fields' => $extraData]))
                     ->setData('records', $i)
                     ->setData('run', 0)
                     ->save();
            }
        }

        if ($customerIdToSegment) {
            $this->setData('extra_data', json_encode(['type' => $type, 'fields' => $extraData]))
                 ->save();
        }

        if ($customerIdToSegment) {
            $conditions = ['revert=1', 'customer_id=?' => $customerIdToSegment];
        } else {
            $conditions = ['revert=1'];
        }

        $write->delete($write->getTableName('panda_segments_records'), $conditions);

        if (!$customerIdToSegment) {
            $this->indexer->updateIndexStatus(Indexer::STATUS_VALID, 'segments');
        }

        return ['emails' => $this->customersEmails, 'data' => $this->customersData];
    }

    /**
     * @param $args
     */
    public function callbackValidateCustomer($args)
    {

        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = clone $args['customer'];
        $customer->setData($args['row']);

        $this->_registry->register('panda_segments_data', new \Magento\Framework\DataObject, true);

        if ($this->getConditions()->validate($customer)) {
            $data = $this->_registry->registry('panda_segments_data');

            if ($customer->getData('email')) {
                $email = $customer->getData('email');
            } else {
                $email = $customer->getData('customer_email');
            }

            if (!$email) {
                $email = $customer->getData('email_meta');
            }

            $this->customersEmails[] = $email;
            $this->customersIds[$email] = $customer->getData();
            $this->customersData[$email] = $data->getData();
        }

        if (!$this->_registry->registry('panda_segments_customer_id')) {
            $this->indexer->updateIndex('customer_' . $this->getId(), $customer->getId(), 'segments');
        }

        if ($output = $this->getData('consoleOutput')) {
            if ($output instanceof \Symfony\Component\Console\Output\OutputInterface) {
                $extra = ' / ' . $this->getData('totalCustomers');

                $output->write(
                    "Segments | Seg: " . $this->getId() . " | Customer: " . $customer->getId() . $extra . "\r"
                );
            }
        }
    }

    /**
     * @return array|mixed
     */
    public function getCustomerGroupIds()
    {

        $ids = $this->getData('customer_group_ids');
        if (($ids && !$this->getCustomerGroupChecked()) || is_string($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            $groupIds = $this->groupCollection->create()->getAllIds();
            $ids = array_intersect($ids, $groupIds);
            $this->setData('customer_group_ids', $ids);
            $this->setCustomerGroupChecked(true);
        }

        return $ids;
    }

    /**
     * @return array
     */
    public function toFormValues()
    {

        $return = [];
        $collection = $this->segmentsCollection->create()
                                               ->addFieldToSelect('segment_id')
                                               ->addFieldToSelect('name');

        foreach ($collection as $segment) {
            $return[$segment->getId()] = $segment->getName();
        }

        return $return;
    }

    /**
     * @param bool $ignore
     *
     * @return array
     */
    public function toOptionArray($ignore = false)
    {

        return $this->getOptionArray($ignore);
    }

    /**
     *
     */
    public function buildUser()
    {

        $segments = $this->segmentsCollection->create()->addFieldToFilter('build', 1);

        /** @var Segments $segment */
        foreach ($segments as $segment) {
            $this->load($segment->getId())->setData('build', 2)->save();

            $segment->updateSegmentRecords();

            $this->load($segment->getId())
                 ->setData('build', 0)
                 ->save();
        }
    }

    /**
     *
     */
    public function cron()
    {

        $date = new \Zend_Date();

        $segments = $this->segmentsCollection->create()
                                             ->addFieldToFilter('cron', ['nin' => ['0']]);

        $segments->getSelect()->where(" cron_last_run <? or cron_last_run IS NULL ", $this->pandaHelper->gmtDate());

        /** @var self $segment */
        foreach ($segments as $segment) {
            $segment->load($segment->getId());

            if ($segment->getCron() == 'd' || $segment->getCron() == 'r') {
                $segment->updateSegmentRecords($segment->getId());
            }

            if ($segment->getCron() == 'w' && $date->get('e') == 1) {
                $segment->updateSegmentRecords($segment->getId());
            }

            if ($segment->getCron() == 'm' && $date->get('d') == 1) {
                $segment->updateSegmentRecords($segment->getId());
            }
        }
    }

    /**
     * @return \Magento\Rule\Model\AbstractModel
     */
    public function afterDelete()
    {

        $this->pandaHelper->renameCustomerListingFile($this);

        return parent::afterDelete();
    }

    /**
     * @return \Magento\Rule\Model\AbstractModel
     */
    public function afterSave()
    {

        $this->pandaHelper->renameCustomerListingFile($this);

        return parent::afterSave();
    }

    /**
     * @return $this|bool|\Magento\Rule\Model\AbstractModel
     */
    public function beforeSave()
    {

        if ($this->getId() && !$this->getData('type')) {
            #$this->setType('customers');
            $this->unsetData('type');
        }

        if (is_array($this->getData('records'))) {
            $this->unsetData('records');
        }

        if ($this->getData('manual') != '1') {
            $this->setManual('0');
        }

        if (!$this->getData('controller_panda')) {
            return parent::beforeSave();
        }

        parent::beforeSave();

        if ($this->getUseAsCatalog() == 0 &&
            $this->getOrigData('use_as_catalog') == 1 &&
            $this->getId()) {

            $this->getResource()
                 ->getConnection()
                 ->delete(
                     $this->getResource()->getTable('panda_segments_products'),
                     ['segment_id=?' => $this->getId()]
                 );

            $this->setNumberProducts(0);
        }

        $attributes = [
            'Magento\SalesRule\Model\Rule\Condition\Product'           => 'order',
            '\Licentia\Equity\Model\Segments\Condition\DefaultAddress' => 'account',
            '\Licentia\Equity\Model\Segments\Condition\Customer'       => 'account',
            '\Licentia\Equity\Model\Segments\Condition\Sku'            => 'order',
            'Licentia\Equity\Model\Segments\Condition\Address'         => 'order',
            'Licentia\Equity\Model\Segments\Condition\Search'          => 'search',
            'Licentia\Equity\Model\Segments\Condition\Subscriber'      => 'subscriber',
        ];

        $activity = [
            'factivity_account_date'                    => 'account',
            'factivity_account'                         => 'account',
            'factivity_last_activity'                   => 'update',
            'factivity_last_activity_date'              => 'update',
            'factivity_number_visits'                   => 'update',
            'factivity_gender'                          => 'account',
            'factivity_age'                             => 'account',
            'factivity_dob'                             => 'account',
            'factivity_anniversary'                     => 'account',
            'factivity_abandoned'                       => 'quote',
            'factivity_cart_totals'                     => 'quote',
            'factivity_cart_number'                     => 'quote',
            'factivity_cart_products'                   => 'quote',
            'factivity_loyal'                           => 'update',
            'factivity_formula_1'                       => 'update',
            'factivity_formula_2'                       => 'update',
            'factivity_formula_3'                       => 'update',
            'factivity_formula_4'                       => 'update',
            'factivity_formula_5'                       => 'update',
            'factivity_formula_6'                       => 'update',
            'factivity_formula_7'                       => 'update',
            'factivity_formula_8'                       => 'update',
            'factivity_formula_9'                       => 'update',
            'factivity_formula_10'                      => 'update',
            'factivity_order_average_days'              => 'order',
            'factivity_last_order'                      => 'order',
            'factivity_first_order'                     => 'order',
            'factivity_pending_payment'                 => 'order',
            'factivity_last_order_date'                 => 'order',
            'factivity_first_order_date'                => 'order',
            'factivity_number_orders'                   => 'order',
            'factivity_number_completed_orders'         => 'order',
            'factivity_number_orders_with_discount'     => 'order',
            'factivity_percentage_orders_with_discount' => 'order',
            'factivity_percentage_complete_orders'      => 'order',
            'factivity_subtotal'                        => 'order',
            'factivity_shipping'                        => 'order',
            'factivity_taxes'                           => 'order',
            'factivity_refunded'                        => 'order',
            'factivity_profit'                          => 'order',
            'factivity_cost'                            => 'order',
            'factivity_discount'                        => 'order',
            'factivity_order_amount'                    => 'order',
            'factivity_order_average'                   => 'order',
            'factivity_order_average_1year'             => 'order',
            'factivity_order_amount_1year'              => 'order',
            'factivity_order_average_older'             => 'order',
            'factivity_order_amount_older'              => 'order',
            'factivity_percentage_order_amount'         => 'order',
            'factivity_visit_category'                  => 'activity',
            'factivity_visit_category_recent'           => 'activity',
            'factivity_visit_category_freq'             => 'activity',
            'factivity_visit_attrs'                     => 'activity',
            'factivity_visit_attrs_recent'              => 'activity',
            'factivity_visit_attrs_freq'                => 'activity',
            'factivity_visit_attrs_bought'              => 'activity',
            'factivity_visit_attrs_bought_recent'       => 'activity',
            'factivity_visit_attrs_bought_freq'         => 'activity',
            'factivity_visit_product'                   => 'activity',
            'factivity_visit_product_recent'            => 'activity',
            'factivity_visit_product_freq'              => 'activity',
            'factivity_visit_product_bought'            => 'order',
            'factivity_visit_product_bought_recent'     => 'order',
            'factivity_visit_product_bought_freq'       => 'order',
            'factivity_last_review'                     => 'review',
            'factivity_last_review_date'                => 'review',
            'factivity_number_reviews'                  => 'review',
        ];

        $toRebuild = [];
        $toRebuild['affects_order'] = 0;
        $toRebuild['affects_product'] = 0;
        $toRebuild['affects_account'] = 0;
        $toRebuild['affects_review'] = 0;
        $toRebuild['affects_subscriber'] = 0;
        $toRebuild['affects_search'] = 0;
        $toRebuild['affects_update'] = 0;

        $conditions = json_decode($this->getData('conditions_serialized'), true);

        if (is_array($conditions)) {
            $t = function ($item, $key) use ($attributes, $activity, &$toRebuild) {

                if (is_string($item) && array_key_exists($item, $attributes) && $key == 'type') {
                    $toRebuild['affects_' . $attributes[$item]] = 1;
                } else {
                    if (is_string($item) && array_key_exists($item, $activity)) {
                        $toRebuild['affects_' . $activity[$item]] = 1;
                    }
                }
            };

            array_walk_recursive($conditions, $t);
        }

        $this->addData($toRebuild);

        return $this;
    }

    /**
     *
     */
    public function updateRealTimeCron()
    {

        $records = $this->connection->fetchAssoc(
            $this->connection->select()
                             ->from(
                                 $this->resource->getTable('panda_segments_update_queue')
                             )
        );

        $distinctCustomerIds = $this->connection->fetchCol(
            $this->connection->select()
                             ->from($this->resource->getTable('panda_segments_update_queue'))
                             ->distinct()
        );

        $this->connection->delete(
            $this->resource->getTable('panda_segments_update_queue'),
            ['process_id IN (?)' => array_keys($records)]
        );

        foreach ($records as $record) {
            $this->load($record['segment_id'])->updateSegmentRecords($record['customer_id']);
        }

        foreach ($distinctCustomerIds as $customerId) {
            $email = $this->connection->fetchOne(
                $this->connection->select()
                                 ->from($this->resource->getTable('customer_entity'), ['email'])
                                 ->where('entity_id=?', $customerId)
            );

            if ($email) {
                $this->formulasFactory->create()->run(false, $email);
            }
        }
    }

    /**
     * @param $eventType
     * @param $customerId
     */
    public function buildForEvent($eventType, $customerId)
    {

        $segments = $this->connection->fetchCol(
            $this->connection->select()
                             ->from($this->resource->getMainTable(), ['segment_id'])
                             ->where('FIND_IN_SET(?,build_after_event)', $eventType)
                             ->where('is_active=?', 1)
                             ->where('manual=?', 0)
        );

        foreach ($segments as $segment) {
            $this->load($segment)->updateSegmentRecords($customerId);
            $this->pandaHelper->getCustomerSegmentsIds(false, true);
        }

    }

    /**
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateBeforeSave()
    {

        if ($this->getCode() && !$this->getOrigData('code')) {
            $unique = $this->getCollection()->addFieldToFilter('code', $this->getCode());

            if ($unique->count() > 0) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Duplicated value for the field: Code'));
            }
        }

        return parent::validateBeforeSave();
    }

    /**
     * @return mixed|null|string
     */
    public function getSegmentId()
    {

        return $this->getData(self::SEGMENT_ID);
    }

    /**
     * @param int $segment_id
     *
     * @return SegmentsInterface|Segments
     */
    public function setSegmentId($segment_id)
    {

        return $this->setData(self::SEGMENT_ID, $segment_id);
    }

    /**
     * @return mixed|null|string
     */
    public function getName()
    {

        return $this->getData(self::NAME);
    }

    /**
     * @return mixed|null|string
     */
    public function getCode()
    {

        return $this->getData(self::CODE);
    }

    /**
     * @param string $name
     *
     * @return SegmentsInterface|Segments
     */
    public function setName($name)
    {

        return $this->setData(self::NAME, $name);
    }

    /**
     * @param string $code
     *
     * @return SegmentsInterface|Segments
     */
    public function setCode($code)
    {

        return $this->setData(self::CODE, $code);
    }

    /**
     * @return int|mixed|null
     */
    public function getIsActive()
    {

        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * @param int $is_active
     *
     * @return SegmentsInterface|Segments
     */
    public function setIsActive($is_active)
    {

        return $this->setData(self::IS_ACTIVE, $is_active);
    }

    /**
     * @return int|mixed|null
     */
    public function getRecords()
    {

        return $this->getData(self::RECORDS);
    }

    /**
     * @param int $records
     *
     * @return SegmentsInterface|Segments
     */
    public function setRecords($records)
    {

        return $this->setData(self::RECORDS, $records);
    }

    /**
     * @return mixed|null|string
     */
    public function getDescription()
    {

        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @param string $description
     *
     * @return SegmentsInterface|Segments
     */
    public function setDescription($description)
    {

        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @return mixed|null|string
     */
    public function getCron()
    {

        return $this->getData(self::CRON);
    }

    /**
     * @param string $cron
     *
     * @return SegmentsInterface|Segments
     */
    public function setCron($cron)
    {

        return $this->setData(self::CRON, $cron);
    }

    /**
     * @return mixed|null|string
     */
    public function getLastUpdate()
    {

        return $this->getData(self::LAST_UPDATE);
    }

    /**
     * @param string $last_update
     *
     * @return SegmentsInterface|Segments
     */
    public function setLastUpdate($last_update)
    {

        return $this->setData(self::LAST_UPDATE, $last_update);
    }

    /**
     * @return mixed|null|string
     */
    public function getType()
    {

        return $this->getData(self::TYPE);
    }

    /**
     * @param string $type
     *
     * @return SegmentsInterface|Segments
     */
    public function setType($type)
    {

        return $this->setData(self::TYPE, $type);
    }

    /**
     * @return int|mixed|null
     */
    public function getManual()
    {

        return $this->getData(self::MANUAL);
    }

    /**
     * @param int $manual
     *
     * @return SegmentsInterface|Segments
     */
    public function setManual($manual)
    {

        return $this->setData(self::MANUAL, $manual);
    }

    /**
     * @return int|mixed|null
     */
    public function getManuallyAdded()
    {

        return $this->getData(self::MANUALLY_ADDED);
    }

    /**
     * @param int $manually_added
     *
     * @return SegmentsInterface|Segments
     */
    public function setManuallyAdded($manually_added)
    {

        return $this->setData(self::MANUALLY_ADDED, $manually_added);
    }

    /**
     * @return mixed|null|string
     */
    public function getFormula1()
    {

        return $this->getData(self::FORMULA_1);
    }

    /**
     * @param string $formula_1
     *
     * @return SegmentsInterface|Segments
     */
    public function setFormula1($formula_1)
    {

        return $this->setData(self::FORMULA_1, $formula_1);
    }

    /**
     * @return mixed|null|string
     */
    public function getFormula2()
    {

        return $this->getData(self::FORMULA_2);
    }

    /**
     * @param string $formula_2
     *
     * @return SegmentsInterface|Segments
     */
    public function setFormula2($formula_2)
    {

        return $this->setData(self::FORMULA_2, $formula_2);
    }

    /**
     * @return mixed|null|string
     */
    public function getFormula3()
    {

        return $this->getData(self::FORMULA_3);
    }

    /**
     * @param string $formula_3
     *
     * @return SegmentsInterface|Segments
     */
    public function setFormula3($formula_3)
    {

        return $this->setData(self::FORMULA_3, $formula_3);
    }

    /**
     * @return mixed|null|string
     */
    public function getFormula4()
    {

        return $this->getData(self::FORMULA_4);
    }

    /**
     * @param string $formula_4
     *
     * @return SegmentsInterface|Segments
     */
    public function setFormula4($formula_4)
    {

        return $this->setData(self::FORMULA_4, $formula_4);
    }

    /**
     * @return mixed|null|string
     */
    public function getFormula5()
    {

        return $this->getData(self::FORMULA_5);
    }

    /**
     * @param string $formula_5
     *
     * @return SegmentsInterface|Segments
     */
    public function setFormula5($formula_5)
    {

        return $this->setData(self::FORMULA_5, $formula_5);
    }

    /**
     * @param $conditionsSerialized
     *
     * @return SegmentsInterface|Segments
     */
    public function setConditionsSerialized($conditionsSerialized)
    {

        return $this->setData('conditions_serialized', $conditionsSerialized);
    }

    /**
     * @param string $run
     *
     * @return SegmentsInterface|Segments
     */
    public function setRun($run)
    {

        return $this->setData('run', $run);
    }

    /**
     * @param string $cronLastRun
     *
     * @return SegmentsInterface|Segments
     */
    public function setCronLastRun($cronLastRun)
    {

        return $this->setData('cron_last_run', $cronLastRun);
    }

    /**
     * @param string $build
     *
     * @return SegmentsInterface|Segments
     */
    public function setBuild($build)
    {

        return $this->setData('build', $build);
    }

    /**
     * @param int $notifyUser
     *
     * @return SegmentsInterface|Segments
     */
    public function setNotifyUser($notifyUser)
    {

        return $this->setData('notify_user', $notifyUser);
    }

    /**
     * @param string $extraData
     *
     * @return SegmentsInterface|Segments
     */
    public function setExtraData($extraData)
    {

        return $this->setData('extra_data', $extraData);
    }

    /**
     * @param string $websitesIds
     *
     * @return SegmentsInterface|Segments
     */
    public function setWebsitesIds($websitesIds)
    {

        return $this->setData('websites_ids', $websitesIds);
    }

    /**
     * @param string $productsRelations
     *
     * @return SegmentsInterface|Segments
     */
    public function setProductsRelations($productsRelations)
    {

        return $this->setData('products_relations', $productsRelations);
    }

    /**
     * @param string $formula_0
     *
     * @return SegmentsInterface|Segments
     */
    public function setFormula0($formula_0)
    {

        return $this->setData('formula_0', $formula_0);
    }

    /**
     * @param string $formula_6
     *
     * @return SegmentsInterface|Segments
     */
    public function setFormula6($formula_6)
    {

        return $this->setData('formula_6', $formula_6);
    }

    /**
     * @param string $formula_7
     *
     * @return SegmentsInterface|Segments
     */
    public function setFormula7($formula_7)
    {

        return $this->setData('formula_7', $formula_7);
    }

    /**
     * @param string $formula_8
     *
     * @return SegmentsInterface|Segments
     */
    public function setFormula8($formula_8)
    {

        return $this->setData('formula_8', $formula_8);
    }

    /**
     * @param string $formula_9
     *
     * @return SegmentsInterface|Segments
     */
    public function setFormula9($formula_9)
    {

        return $this->setData('formula_9', $formula_9);
    }

    /**
     * @param string $formula_10
     *
     * @return SegmentsInterface|Segments
     */
    public function setFormula10($formula_10)
    {

        return $this->setData('formula_10', $formula_10);
    }

    /**
     * @param string $numberOrders
     *
     * @return SegmentsInterface|Segments
     */
    public function setNumberOrders($numberOrders)
    {

        return $this->setData('number_orders', $numberOrders);
    }

    /**
     * @param string $numberProducts
     *
     * @return SegmentsInterface|Segments
     */
    public function setNumberProducts($numberProducts)
    {

        return $this->setData('number_products', $numberProducts);
    }

    /**
     * @param string $numberCompletedOrders
     *
     * @return SegmentsInterface|Segments
     */
    public function setNumberCompletedOrders($numberCompletedOrders)
    {

        return $this->setData('number_completed_orders', $numberCompletedOrders);
    }

    /**
     * @param float $orderAmount
     *
     * @return SegmentsInterface|Segments
     */
    public function setOrderAmount($orderAmount)
    {

        return $this->setData('order_amount', $orderAmount);
    }

    /**
     * @param float $orderAverage
     *
     * @return SegmentsInterface|Segments
     */
    public function setOrderAverage($orderAverage)
    {

        return $this->setData('order_average', $orderAverage);
    }

    /**
     * @param int $percentageCompleteOrders
     *
     * @return SegmentsInterface|Segments
     */
    public function setPercentageCompleteOrders($percentageCompleteOrders)
    {

        return $this->setData('percentage_complete_orders', $percentageCompleteOrders);
    }

    /**
     * @param int $percentageOrderAmount
     *
     * @return SegmentsInterface|Segments
     */
    public function setPercentageOrderAmount($percentageOrderAmount)
    {

        return $this->setData('percentage_order_amount', $percentageOrderAmount);
    }

    /**
     * @param float $orderAverage_1year
     *
     * @return SegmentsInterface|Segments
     */
    public function setOrderAverage1year($orderAverage_1year)
    {

        return $this->setData('order_average_1year', $orderAverage_1year);
    }

    /**
     * @param float $orderAmount_1year
     *
     * @return SegmentsInterface|Segments
     */
    public function setOrderAmount1year($orderAmount_1year)
    {

        return $this->setData('order_amount_1year', $orderAmount_1year);
    }

    /**
     * @param float $orderAverageOlder
     *
     * @return SegmentsInterface|Segments
     */
    public function setOrderAverageOlder($orderAverageOlder)
    {

        return $this->setData('order_average_older', $orderAverageOlder);
    }

    /**
     * @param float $orderAmountOlder
     *
     * @return SegmentsInterface|Segments
     */
    public function setOrderAmountOlder($orderAmountOlder)
    {

        return $this->setData('order_amount_older', $orderAmountOlder);
    }

    /**
     * @param int $orderAverageDays
     *
     * @return SegmentsInterface|Segments
     */
    public function setOrderAverageDays($orderAverageDays)
    {

        return $this->setData('order_average_days', $orderAverageDays);
    }

    /**
     * @param float $numberOrdersWithDiscount
     *
     * @return SegmentsInterface|Segments
     */
    public function setNumberOrdersWithDiscount($numberOrdersWithDiscount)
    {

        return $this->setData('number_orders_with_discount', $numberOrdersWithDiscount);
    }

    /**
     * @param float $shipping
     *
     * @return SegmentsInterface|Segments
     */
    public function setShipping($shipping)
    {

        return $this->setData('shipping', $shipping);
    }

    /**
     * @param float $taxes
     *
     * @return SegmentsInterface|Segments
     */
    public function setTaxes($taxes)
    {

        return $this->setData('taxes', $taxes);
    }

    /**
     * @param float $subtotal
     *
     * @return SegmentsInterface|Segments
     */
    public function setSubtotal($subtotal)
    {

        return $this->setData('subtotal', $subtotal);
    }

    /**
     * @param float $discount
     *
     * @return SegmentsInterface|Segments
     */
    public function setDiscount($discount)
    {

        return $this->setData('discount', $discount);
    }

    /**
     * @param float $cost
     *
     * @return SegmentsInterface|Segments
     */
    public function setCost($cost)
    {

        return $this->setData('cost', $cost);
    }

    /**
     * @param float $profit
     *
     * @return SegmentsInterface|Segments
     */
    public function setProfit($profit)
    {

        return $this->setData('profit', $profit);
    }

    /**
     * @param float $refunded
     *
     * @return SegmentsInterface|Segments
     */
    public function setRefunded($refunded)
    {

        return $this->setData('refunded', $refunded);
    }

    /**
     * @param int $lastOrder
     *
     * @return SegmentsInterface|Segments
     */
    public function setLastOrder($lastOrder)
    {

        return $this->setData('last_order', $lastOrder);
    }

    /**
     * @param int $firstOrder
     *
     * @return SegmentsInterface|Segments
     */
    public function setFirstOrder($firstOrder)
    {

        return $this->setData('first_order', $firstOrder);
    }

    /**
     * @param int $lastActivity
     *
     * @return SegmentsInterface|Segments
     */
    public function setLastActivity($lastActivity)
    {

        return $this->setData('last_activity', $lastActivity);
    }

    /**
     * @param int $age
     *
     * @return SegmentsInterface|Segments
     */
    public function setAge($age)
    {

        return $this->setData('age', $age);
    }

    /**
     * @param int $abandoned
     *
     * @return SegmentsInterface|Segments
     */
    public function setAbandoned($abandoned)
    {

        return $this->setData('abandoned', $abandoned);
    }

    /**
     * @param float $cartTotals
     *
     * @return SegmentsInterface|Segments
     */
    public function setCartTotals($cartTotals)
    {

        return $this->setData('cart_totals', $cartTotals);
    }

    /**
     * @param int $cartNumber
     *
     * @return SegmentsInterface|Segments
     */
    public function setCartNumber($cartNumber)
    {

        return $this->setData('cart_number', $cartNumber);
    }

    /**
     * @param int $cartProducts
     *
     * @return SegmentsInterface|Segments
     */
    public function setCartProducts($cartProducts)
    {

        return $this->setData('cart_products', $cartProducts);
    }

    /**
     * @param int $pendingPayment
     *
     * @return SegmentsInterface|Segments
     */
    public function setPendingPayment($pendingPayment)
    {

        return $this->setData('pending_payment', $pendingPayment);
    }

    /**
     * @param int $account
     *
     * @return SegmentsInterface|Segments
     */
    public function setAccount($account)
    {

        return $this->setData('account', $account);
    }

    /**
     * @param int $lastReview
     *
     * @return SegmentsInterface|Segments
     */
    public function setLastReview($lastReview)
    {

        return $this->setData('last_review', $lastReview);
    }

    /**
     * @param int $numberReviews
     *
     * @return SegmentsInterface|Segments
     */
    public function setNumberReviews($numberReviews)
    {

        return $this->setData('number_reviews', $numberReviews);
    }

    /**
     * @param string $skuBought
     *
     * @return SegmentsInterface|Segments
     */
    public function setSkuBought($skuBought)
    {

        return $this->setData('sku_bought', $skuBought);
    }

    /**
     * @param bool $affectsOrder
     *
     * @return SegmentsInterface|Segments
     */
    public function setAffectsOrder($affectsOrder)
    {

        return $this->setData('affects_order', $affectsOrder);
    }

    /**
     * @param bool $affectsProduct
     *
     * @return SegmentsInterface|Segments
     */
    public function setAffectsProduct($affectsProduct)
    {

        return $this->setData('affects_product', $affectsProduct);
    }

    /**
     * @param bool $affectsQuote
     *
     * @return SegmentsInterface|Segments
     */
    public function setAffectsQuote($affectsQuote)
    {

        return $this->setData('affects_quote', $affectsQuote);
    }

    /**
     * @param bool $affectsAccount
     *
     * @return SegmentsInterface|Segments
     */
    public function setAffectsAccount($affectsAccount)
    {

        return $this->setData('affects_account', $affectsAccount);
    }

    /**
     * @param bool $affectsReview
     *
     * @return SegmentsInterface|Segments
     */
    public function setAffectsReview($affectsReview)
    {

        return $this->setData('affects_review', $affectsReview);
    }

    /**
     * @param bool $affectsSearch
     *
     * @return SegmentsInterface|Segments
     */
    public function setAffectsSearch($affectsSearch)
    {

        return $this->setData('affects_search', $affectsSearch);
    }

    /**
     * @param bool $affectsSubscriber
     *
     * @return SegmentsInterface|Segments
     */
    public function setAffectsSubscriber($affectsSubscriber)
    {

        return $this->setData('affects_subscriber', $affectsSubscriber);
    }

    /**
     * @param bool $affectsUpdate
     *
     * @return SegmentsInterface|Segments
     */
    public function setAffectsUpdate($affectsUpdate)
    {

        return $this->setData('affects_update', $affectsUpdate);
    }

    /**
     * @return mixed|null|string
     */
    public function getConditionsSerialized()
    {

        return $this->getData('conditions_serialized');
    }

    /**
     * @return mixed|null|string
     */
    public function getRun()
    {

        return $this->getData('run');
    }

    /**
     * @return mixed|null|string
     */
    public function getCronLastRun()
    {

        return $this->getData('cron_last_run');
    }

    /**
     * @return mixed|null|string
     */
    public function getBuild()
    {

        return $this->getData('build');
    }

    /**
     * @return int|mixed|null
     */
    public function getNotifyUser()
    {

        return $this->getData('notify_user');
    }

    /**
     * @return mixed|null|string
     */
    public function getExtraData()
    {

        return $this->getData('extra_data');
    }

    /**
     * @return mixed|null|string
     */
    public function getWebsitesIds()
    {

        return $this->getData('websites_ids');
    }

    /**
     * @return mixed|null|string
     */
    public function getProductsRelations()
    {

        return $this->getData('products_relations');
    }

    /**
     * @return mixed|null|string
     */
    public function getFormula0()
    {

        return $this->getData('formula_0');
    }

    /**
     * @return mixed|null|string
     */
    public function getFormula6()
    {

        return $this->getData('formula_6');
    }

    /**
     * @return mixed|null|string
     */
    public function getFormula7()
    {

        return $this->getData('formula_7');
    }

    /**
     * @return mixed|null|string
     */
    public function getFormula8()
    {

        return $this->getData('formula_8');
    }

    /**
     * @return mixed|null|string
     */
    public function getFormula9()
    {

        return $this->getData('formula_9');
    }

    /**
     * @return mixed|null|string
     */
    public function getFormula10()
    {

        return $this->getData('formula_10');
    }

    /**
     * @return int|mixed|null
     */
    public function getNumberOrders()
    {

        return $this->getData('number_orders');
    }

    /**
     * @return int|mixed|null
     */
    public function getNumberProducts()
    {

        return $this->getData('number_products');
    }

    /**
     * @return int|mixed|null
     */
    public function getNumberCompletedOrders()
    {

        return $this->getData('number_completed_orders');
    }

    /**
     * @return float|mixed|null
     */
    public function getOrderAmount()
    {

        return $this->getData('order_amount');
    }

    /**
     * @return float|mixed|null
     */
    public function getOrderAverage()
    {

        return $this->getData('order_average');
    }

    /**
     * @return int|mixed|null
     */
    public function getPercentageCompleteOrders()
    {

        return $this->getData('percentage_complete_orders');
    }

    /**
     * @return int|mixed|null
     */
    public function getPercentageOrderAmount()
    {

        return $this->getData('percentage_order_amount');
    }

    /**
     * @return float|mixed|null
     */
    public function getOrderAverage1year()
    {

        return $this->getData('order_average_1year');
    }

    /**
     * @return float|mixed|null
     */
    public function getOrderAmount1year()
    {

        return $this->getData('order_amount_1year');
    }

    /**
     * @return float|mixed|null
     */
    public function getOrderAverageOlder()
    {

        return $this->getData('order_average_older');
    }

    /**
     * @return float|mixed|null
     */
    public function getOrderAmountOlder()
    {

        return $this->getData('order_amount_older');
    }

    /**
     * @return int|mixed|null
     */
    public function getOrderAverageDays()
    {

        return $this->getData('order_average_days');
    }

    /**
     * @return int|mixed|null
     */
    public function getNumberOrdersWithDiscount()
    {

        return $this->getData('number_orders_with_discount');
    }

    /**
     * @return float|mixed|null
     */
    public function getShipping()
    {

        return $this->getData('shipping');
    }

    /**
     * @return float|mixed|null
     */
    public function getTaxes()
    {

        return $this->getData('taxes');
    }

    /**
     * @return float|mixed|null
     */
    public function getSubtotal()
    {

        return $this->getData('subtotal');
    }

    /**
     * @return float|mixed|null
     */
    public function getDiscount()
    {

        return $this->getData('discount');
    }

    /**
     * @return float|mixed|null
     */
    public function getCost()
    {

        return $this->getData('cost');
    }

    /**
     * @return float|mixed|null
     */
    public function getProfit()
    {

        return $this->getData('profit');
    }

    /**
     * @return float|mixed|null
     */
    public function getRefunded()
    {

        return $this->getData('refunded');
    }

    /**
     * @return int|mixed|null
     */
    public function getLastOrder()
    {

        return $this->getData('last_order');
    }

    /**
     * @return int|mixed|null
     */
    public function getFirstOrder()
    {

        return $this->getData('first_order');
    }

    /**
     * @return int|mixed|null
     */
    public function getLastActivity()
    {

        return $this->getData('last_activity');
    }

    /**
     * @return int|mixed|null
     */
    public function getAge()
    {

        return $this->getData('age');
    }

    /**
     * @return int|mixed|null
     */
    public function getAbandoned()
    {

        return $this->getData('abandoned');
    }

    /**
     * @return float|mixed|null
     */
    public function getCartTotals()
    {

        return $this->getData('cart_totals');
    }

    /**
     * @return int|mixed|null
     */
    public function getCartNumber()
    {

        return $this->getData('cart_number');
    }

    /**
     * @return int|mixed|null
     */
    public function getCartProducts()
    {

        return $this->getData('cart_products');
    }

    /**
     * @return int|mixed|null
     */
    public function getPendingPayment()
    {

        return $this->getData('pending_payment');
    }

    /**
     * @return int|mixed|null
     */
    public function getAccount()
    {

        return $this->getData('account');
    }

    /**
     * @return int|mixed|null
     */
    public function getLastReview()
    {

        return $this->getData('last_review');
    }

    /**
     * @return int|mixed|null
     */
    public function getNumberReviews()
    {

        return $this->getData('number_reviews');
    }

    /**
     * @return mixed|null|string
     */
    public function getSkuBought()
    {

        return $this->getData('sku_bought');
    }

    /**
     * @return bool|mixed|null
     */
    public function getAffectsOrder()
    {

        return $this->getData('affects_order');
    }

    /**
     * @return bool|mixed|null
     */
    public function getAffectsProduct()
    {

        return $this->getData('affects_product');
    }

    /**
     * @return bool|mixed|null
     */
    public function getAffectsQuote()
    {

        return $this->getData('affects_quote');
    }

    /**
     * @return bool|mixed|null
     */
    public function getAffectsAccount()
    {

        return $this->getData('affects_account');
    }

    /**
     * @return bool|mixed|null
     */
    public function getAffectsReview()
    {

        return $this->getData('affects_review');
    }

    /**
     * @return bool|mixed|null
     */
    public function getAffectsSearch()
    {

        return $this->getData('affects_search');
    }

    /**
     * @return bool|mixed|null
     */
    public function getAffectsSubscriber()
    {

        return $this->getData('affects_subscriber');
    }

    /**
     * @return bool|mixed|null
     */
    public function getAffectsUpdate()
    {

        return $this->getData('affects_update');
    }

    /**
     * @param bool $realTimeUpdateCron
     *
     * @return SegmentsInterface|Segments
     */
    public function setRealTimeUpdateCron($realTimeUpdateCron)
    {

        return $this->setData('real_time_update_cron', $realTimeUpdateCron);
    }

    /**
     * @return bool|mixed|null
     */
    public function getRealTimeUpdateCron()
    {

        return $this->getData('real_time_update_cron');
    }

    /**
     * @param bool $useAsCatalog
     *
     * @return SegmentsInterface|Segments
     */
    public function setUseAsCatalog($useAsCatalog)
    {

        return $this->setData('use_as_catalog', $useAsCatalog);
    }

    /**
     * @return bool|mixed|null
     */
    public function getUseAsCatalog()
    {

        return $this->getData('use_as_catalog');
    }
}
