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

namespace Licentia\Equity\Model\Segments\Condition;

use Magento\Customer\Model\Session;
use Magento\Rule\Model\Condition\Context;

/**
 * Class Activity
 *
 * @package Licentia\Equity\Model\Segments\Condition
 */
class Activity extends AbstractCondition
{

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var \Licentia\Equity\Model\KpisFactory
     */
    protected $kpisFactory;

    /**
     * @var \Licentia\Equity\Model\FormulasFactory
     */
    protected $formulasFactory;

    /**
     * @var \Magento\Directory\Model\Config\Source\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var \Licentia\Equity\Model\SegmentsFactory
     */
    protected $segmentsFactory;

    /**
     * @var \Licentia\Equity\Model\Segments\ListSegmentsFactory
     */
    protected $listsegmentsFactory;

    /**
     * Activity constructor.
     *
     * @param Context                                                         $context
     * @param \Magento\Directory\Model\Config\Source\CountryFactory           $countryFactory
     * @param \Licentia\Equity\Helper\Data                                    $pandaHelper
     * @param \Licentia\Equity\Model\SegmentsFactory                          $segmentsFactory
     * @param \Licentia\Equity\Model\Segments\ListSegmentsFactory             $listSegmentsFactory
     * @param \Licentia\Equity\Model\KpisFactory                              $kpisFactory
     * @param \Licentia\Equity\Model\FormulasFactory                          $formulasFactory
     * @param Session                                                         $customerSession
     * @param \Magento\Backend\Helper\Data                                    $backendData
     * @param \Magento\Framework\Registry                                     $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface              $scopeInterface
     * @param \Licentia\Equity\Model\ResourceModel\Segments                   $segmentsResource
     * @param \Magento\Catalog\Model\ProductFactory                           $productFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory  $productCollection
     * @param \Magento\Catalog\Model\CategoryFactory                          $categoryFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory      $quoteCollection
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory      $orderCollection
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemCollection
     * @param array                                                           $data
     */
    public function __construct(
        Context $context,
        \Magento\Directory\Model\Config\Source\CountryFactory $countryFactory,
        \Licentia\Equity\Helper\Data $pandaHelper,
        \Licentia\Equity\Model\SegmentsFactory $segmentsFactory,
        \Licentia\Equity\Model\Segments\ListSegmentsFactory $listSegmentsFactory,
        \Licentia\Equity\Model\KpisFactory $kpisFactory,
        \Licentia\Equity\Model\FormulasFactory $formulasFactory,
        Session $customerSession,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface,
        \Licentia\Equity\Model\ResourceModel\Segments $segmentsResource,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollection,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemCollection,
        array $data = []
    ) {

        $this->countryFactory = $countryFactory;
        $this->kpisFactory = $kpisFactory;
        $this->customerSession = $customerSession;
        $this->formulasFactory = $formulasFactory;
        $this->listsegmentsFactory = $listSegmentsFactory;
        $this->segmentsFactory = $segmentsFactory;
        parent::__construct(
            $context,
            $backendData,
            $pandaHelper,
            $registry,
            $scopeInterface,
            $segmentsResource,
            $productFactory,
            $productCollection,
            $categoryFactory,
            $quoteCollection,
            $orderCollection,
            $itemCollection,
            $data
        );
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function loadAttributeOptions()
    {

        /** @var \Licentia\Equity\Model\Formulas $formulas */
        $formulas = $this->formulasFactory->create()->getFormulas();
        $attributes = [
            'factivity_account_date'                    => __('Account - Account Registration Date'),
            'factivity_account'                         => __('Account - Days since Registration'),
            'factivity_last_activity'                   => __('Activity - Days Since Last Activity'),
            'factivity_last_activity_date'              => __('Activity - Last Activity Date'),
            'factivity_number_visits'                   => __('Activity - Number of visits'),
            'factivity_gender'                          => __('Gender - Customer Gender'),
            'factivity_age'                             => __('Age - Customer Age'),
            'factivity_dob'                             => __('Age - Customer Date of Birth'),
            'factivity_anniversary'                     => __('Age - Days to anniversary'),
            'factivity_abandoned'                       => __('Current Shopping Cart - Days with an abandoned cart'),
            'factivity_cart_totals'                     => __('Current Shopping Cart - Shopping Cart Total'),
            'factivity_cart_number'                     => __(
                'Current Shopping Cart - Number of Products In Shopping Cart'
            ),
            'factivity_cart_products'                   => __('Current Shopping Cart - Products Qty in Shopping Cart'),
            'factivity_loyal'                           => __('Equity - Customer is Loyal'),
            'factivity_formula_1'                       => __('Equity - ') . $formulas->getFormula1Name(),
            'factivity_formula_2'                       => __('Equity - ') . $formulas->getFormula2Name(),
            'factivity_formula_3'                       => __('Equity - ') . $formulas->getFormula3Name(),
            'factivity_formula_4'                       => __('Equity - ') . $formulas->getFormula4Name(),
            'factivity_formula_5'                       => __('Equity - ') . $formulas->getFormula5Name(),
            'factivity_formula_6'                       => __('Equity - ') . $formulas->getFormula6Name(),
            'factivity_formula_7'                       => __('Equity - ') . $formulas->getFormula7Name(),
            'factivity_formula_8'                       => __('Equity - ') . $formulas->getFormula8Name(),
            'factivity_formula_9'                       => __('Equity - ') . $formulas->getFormula9Name(),
            'factivity_formula_10'                      => __('Equity - ') . $formulas->getFormula10Name(),
            'factivity_order_average_days'              => __('Order - Average Days Between Orders'),
            'factivity_last_order'                      => __('Order - Days since last complete order'),
            'factivity_first_order'                     => __('Order - Days since first complete order'),
            'factivity_pending_payment'                 => __('Order - Days with a pending payment for an order'),
            'factivity_last_order_date'                 => __('Order - Last complete order date'),
            'factivity_first_order_date'                => __('Order - First Complete Order Date'),
            'factivity_number_orders'                   => __('Order - Number of Orders'),
            'factivity_number_completed_orders'         => __('Order - Number of Completed Orders'),
            'factivity_number_orders_with_discount'     => __('Order - Number of Completed Orders W/ Discount'),
            'factivity_percentage_orders_with_discount' => __('Order - Percentage Orders W/ Discount'),
            'factivity_percentage_complete_orders'      => __('Order - Percentage of Completed Orders'),
            'factivity_subtotal'                        => __('Order - Lifetime Sales Subtotal'),
            'factivity_shipping'                        => __('Order - Lifetime Sales Shipping'),
            'factivity_taxes'                           => __('Order - Lifetime Sales Taxes'),
            'factivity_refunded'                        => __('Order - Lifetime Sales Refunded'),
            'factivity_profit'                          => __('Order - Lifetime Sales Profit'),
            'factivity_cost'                            => __('Order - Lifetime Sales Cost'),
            'factivity_discount'                        => __('Order - Lifetime Sales Discount'),
            'factivity_order_amount'                    => __('Order - Lifetime Sales Amount'),
            'factivity_order_average'                   => __('Order - Lifetime Sales Average'),
            'factivity_order_average_1year'             => __('Order - Order Average for the last 1 year'),
            'factivity_order_amount_1year'              => __('Order - Order Amount for the last 1 year'),
            'factivity_order_average_older'             => __('Order - Order Average previous to the last 1 year'),
            'factivity_order_amount_older'              => __('Order - Order Amount previous to the last 1 year'),
            'factivity_percentage_order_amount'         => __('Order - Percentage of Global Average Order Amount'),
            'factivity_visit_category'                  => __('Products - Viewed Products in the category'),
            'factivity_visit_category_recent'           => __(
                'Products - Viewed Products in the last 7 days in the category'
            ),
            'factivity_visit_category_freq'             => __('Products - Views Products frequently in the category'),
            'factivity_visit_attrs'                     => __('Products - Viewed Products with the attribute'),
            'factivity_visit_attrs_recent'              => __(
                'Products - Viewed Products in the last 7 days with the attribute'
            ),
            'factivity_visit_attrs_freq'                => __(
                'Products - Views Products frequently with the attribute'
            ),
            'factivity_visit_attrs_bought'              => __('Products - Bought Products with the attribute'),
            'factivity_visit_attrs_bought_recent'       => __(
                'Products - Bought Products in the last 7 days with the attribute'
            ),
            'factivity_visit_attrs_bought_freq'         => __(
                'Products - Bought Products frequently with the attribute'
            ),
            'factivity_visit_product'                   => __('Products - Viewed products with SKU'),
            'factivity_visit_product_recent'            => __('Products - Viewed products with SKU in the last 7 days'),
            'factivity_visit_product_freq'              => __('Products - Viewed products with SKU frequently'),
            'factivity_visit_product_bought'            => __('Products - Bought products with SKU'),
            'factivity_visit_product_bought_recent'     => __('Products - Bought products with SKU in the last 7 days'),
            'factivity_visit_product_bought_freq'       => __('Products - Bought products with SKU frequently'),
            'factivity_last_review'                     => __('Reviews - Days since last review'),
            'factivity_last_review_date'                => __('Reviews - Last Review Date'),
            'factivity_number_reviews'                  => __('Reviews - Number of Reviews'),
            'factivity_presence_segment'                => __('Segments - Presence in Segment'),
        ];

        asort($attributes);

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @return AbstractCondition
     */
    public function getAttributeElement()
    {

        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }

    /**
     * @return string
     */
    public function getInputType()
    {

        if (stripos($this->getAttribute(), '_date') !== false) {
            return 'date';
        }

        switch ($this->getAttribute()) {
            case 'factivity_loyal':
            case 'factivity_gender':
            case 'factivity_presence_segment':
                return 'select';
            case 'factivity_visit_product':
            case 'factivity_visit_product_recent':
            case 'factivity_visit_product_freq':
            case 'factivity_visit_product_bought':
            case 'factivity_visit_product_bought_recent':
            case 'factivity_visit_product_bought_freq':
                return 'string';
            case 'factivity_visit_attrs':
            case 'factivity_visit_attrs_freq':
            case 'factivity_visit_attrs_recent':
            case 'factivity_visit_attrs_bought':
            case 'factivity_visit_attrs_bought_freq':
            case 'factivity_visit_attrs_bought_recent':
            case 'factivity_visit_category':
            case 'factivity_visit_category_freq':
            case 'factivity_visit_category_recent':
                return 'multiselect';
        }

        return 'numeric';
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {

        if (stripos($this->getAttribute(), '_date') !== false) {
            return 'date';
        }

        switch ($this->getAttribute()) {
            case 'factivity_loyal':
            case 'factivity_gender':
            case 'factivity_presence_segment':
                return 'select';
            case 'factivity_visit_product':
            case 'factivity_visit_product_recent':
            case 'factivity_visit_product_freq':
            case 'factivity_visit_product_bought':
            case 'factivity_visit_product_bought_recent':
            case 'factivity_visit_product_bought_freq':
                return 'text';
            case 'factivity_visit_category':
            case 'factivity_visit_category_freq':
            case 'factivity_visit_category_recent':
            case 'factivity_visit_attrs':
            case 'factivity_visit_attrs_freq':
            case 'factivity_visit_attrs_recent':
            case 'factivity_visit_attrs_bought':
            case 'factivity_visit_attrs_bought_freq':
            case 'factivity_visit_attrs_bought_recent':
                return 'multiselect';
        }

        return 'text';
    }

    /**
     * @return mixed
     */
    public function getValueSelectOptions()
    {

        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'factivity_visit_category':
                case 'factivity_visit_category_freq':
                case 'factivity_visit_category_recent':
                    $options = $this->pandaHelper->getCategories();
                    break;
                case 'factivity_loyal':
                    $options = [
                        ['value' => '0', 'label' => __('No')],
                        ['value' => '1', 'label' => __('Yes')],
                    ];
                    break;
                case 'factivity_presence_segment':
                    $options = $this->segmentsFactory->create()
                                                     ->getCollection()
                                                     ->addFieldToFilter('is_active', 1)
                                                     ->toOptionArray();
                    break;
                case 'factivity_gender':
                    $options = [
                        ['value' => 'male', 'label' => __('Male')],
                        ['value' => 'female', 'label' => __('Female')],
                    ];
                    break;
                case 'factivity_visit_attrs':
                case 'factivity_visit_attrs_freq':
                case 'factivity_visit_attrs_recent':
                case 'factivity_visit_attrs_bought':
                case 'factivity_visit_attrs_bought_freq':
                case 'factivity_visit_attrs_bought_recent':
                    $options = $this->pandaHelper->getAttributes();
                    break;
                default:
                    $options = [];
            }
            $this->setData('value_select_options', $options);
        }

        return $this->getData('value_select_options');
    }

    /**
     * Validate Address Rule Condition
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $object)
    {

        $resultData = $this->registry->registry('panda_segments_data');

        if ($registryEmail = $this->registry->registry('panda_segments_email')) {
            $model = $this->kpisFactory->create()->load($registryEmail, 'email_meta');
            $object->setData($model->getData());
        } else {
            if (!$resultData) {
                $resultData = new \Magento\Framework\DataObject();

                if (!$this->customerSession->getCustomerId()) {
                    return false;
                }
                $model = $this->kpisFactory->create()->load($this->customerSession->getCustomerId(), 'customer_id');
                $object->setData($model->getData());
            }
        }

        $dbAttrName = str_replace('factivity_', '', $this->getAttribute());

        $recentInfo = $this->scopeConfig->getValue(
            'panda_magna/segments/recent',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
        $percentageInfo = $this->scopeConfig->getValue(
            'panda_magna/segments/percentage',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );

        $this->setAttribute($dbAttrName);

        if (in_array(
            $this->getAttribute(),
            [
                'subtotal',
                'profit',
                'discount',
                'taxes',
                'cost',
                'refunded',
                'order_amount',
                'order_average',
                'order_average_1year',
                'order_amount_1year',
                'order_average_older',
                'order_amount_older',
            ]
        )) {
            $dataT = 'currency';
        } else {
            $dataT = 'number';
        }

        if ($this->getAttribute() == 'gender') {
            $dataT = 'options';
        }

        if ($dbAttrName == 'presence_segment') {
            $select = $this->resource->getConnection()
                                     ->select()
                                     ->from($this->resource->getTable('panda_segments_records', ['record_id']))
                                     ->where('email=?', $object->getEmail())
                                     ->where('segment_id = ?', $this->getValue());

            $result = $this->resource->getConnection()->fetchOne($select);

            if (($result && $this->getOperator() == '==') || (!$result && $this->getOperator() == '!=')) {
                $resultData->setData((string) $this->getAttributeOption('factivity_' . $dbAttrName), 'OK');

                return true;
            } else {
                return false;
            }

        }

        $valueToLog = is_array($this->getValue()) ? implode(',', $this->getValue()) : $this->getValue();

        if (stripos($dbAttrName, 'visit_product') !== false) {
            $condition = $this->translateOperator();

            if (stripos($condition, 'IN') !== false) {
                $condition = $condition . ' (?)';

                if (!is_array($this->getValue())) {
                    $values = str_getcsv($this->getValue());
                    $values = array_map('trim', $values);
                } else {
                    $values = $this->getValue();
                }
                $this->setValue($values);
            } else {
                $condition = $condition . ' ? ';
            }

            if (stripos($dbAttrName, 'bought') !== false) {
                $fieldName = 'bought';
                $fieldNameDate = 'bought_at';
            } else {
                $fieldName = 'views';
                $fieldNameDate = 'visited_at';
            }

            $table = $this->resource->getTable('panda_segments_metadata_products');

            if (stripos($dbAttrName, '_freq') !== false) {
                $select = $this->resource->getConnection()
                                         ->select()
                                         ->from($table, ['product_id', $fieldName])
                                         ->where($fieldName . ' >=1')
                                         ->where('customer_id=?', $object->getId());

                $result = $this->resource->getConnection()->fetchPairs($select);

                if (!$result) {
                    return false;
                }

                $total = array_sum($result);
                $user = array_sum(array_intersect_key($result, array_flip((array) $this->getValue())));
                if ($total > 0) {
                    $perc = round($user * 100 / $total);
                } else {
                    $perc = 0;
                }

                if ($perc >= $percentageInfo) {
                    $resultData->setData((string) $this->getAttributeOption('factivity_' . $dbAttrName), $valueToLog);

                    return true;
                } else {
                    return false;
                }
            }

            $select = $this->resource->getConnection()
                                     ->select()
                                     ->from($table)
                                     ->where('customer_id=?', $object->getId())
                                     ->where('sku ' . $condition, $this->getValue());

            if (stripos($dbAttrName, '_recent') !== false) {
                $select->where(
                    $fieldNameDate . ' >= date_sub(?, INTERVAL ' . (int) $recentInfo . ' DAY)  ',
                    $this->pandaHelper->gmtDate()
                );
            }

            $select->where($fieldName . ' >=1');

            $result = $this->resource->getConnection()->fetchRow($select);

            if ($result) {
                $resultData->setData((string) $this->getAttributeOption('factivity_' . $dbAttrName), $valueToLog);

                return true;
            } else {
                return false;
            }
        }

        if (stripos($dbAttrName, 'visit_category') !== false) {
            $condition = $this->translateOperator();

            $table = $this->resource->getTable('panda_segments_metadata_categories');

            if (stripos($dbAttrName, '_freq') !== false) {
                $select = $this->resource->getConnection()
                                         ->select()
                                         ->from($table, ['category_id', 'views'])
                                         ->where('customer_id=?', $object->getId());

                $result = $this->resource->getConnection()->fetchPairs($select);

                if (!$result) {
                    return false;
                }

                $total = array_sum($result);
                $user = array_sum(array_intersect_key($result, array_flip((array) $this->getValue())));
                if ($total > 0) {
                    $perc = round($user * 100 / $total);
                } else {
                    $perc = 0;
                }

                if ($perc >= $percentageInfo) {
                    $valueToLog = [];
                    foreach ($result as $cat => $views) {
                        $valueToLog[] = $this->getCategoryName($cat);
                    }
                    $valueToLog = implode(', ', $valueToLog);

                    $resultData->setData((string) $this->getAttributeOption('factivity_' . $dbAttrName), $valueToLog);

                    return true;
                } else {
                    return false;
                }
            }

            $select = $this->resource->getConnection()
                                     ->select()
                                     ->from($table, ['category_id'])
                                     ->where('customer_id=?', $object->getId())
                                     ->where('category_id ' . $condition . '(?)', $this->getValue());

            if (stripos($dbAttrName, '_recent') !== false) {
                $select->where(
                    'visited_date  >= date_sub(?, INTERVAL ' . (int) $recentInfo . ' DAY)  ',
                    $this->pandaHelper->gmtDate()
                );
            }

            $result = $this->resource->getConnection()->fetchCol($select);

            if ($result) {
                $valueToLog = [];
                foreach ($result as $categoryId) {
                    $valueToLog[] = $this->getCategoryName($categoryId);
                }
                $valueToLog = implode(', ', $valueToLog);

                $resultData->setData((string) $this->getAttributeOption('factivity_' . $dbAttrName), $valueToLog);

                return true;
            } else {
                return false;
            }
        }

        if (stripos($dbAttrName, 'visit_attrs') !== false) {
            $attribute = [];
            $option = [];
            $comp = [];

            foreach ($this->getValue() as $value) {
                $tmp = explode('-', $value);
                $option[] = $tmp[1];
                $attribute[] = $tmp[0];
                $comp[$tmp[0]] = $tmp[1];
            }

            $fieldSearch = stripos($dbAttrName, 'bought') !== false ? 'bought' : 'views';

            $condition = $this->translateOperator();

            $table = $this->resource->getTable('panda_segments_metadata_attrs');

            if (stripos($dbAttrName, '_freq') !== false) {
                $select = $this->resource->getConnection()
                                         ->select()
                                         ->from($table, ['option_id', $fieldSearch])
                                         ->where('customer_id=?', $object->getId())
                                         ->where($fieldSearch . ' IS NOT NULL');

                $result = $this->resource->getConnection()->fetchPairs($select);

                $total = array_sum($result);
                $user = array_sum(array_intersect_key($result, array_flip($option)));
                if ($total > 0) {
                    $perc = round($user * 100 / $total);
                } else {
                    $perc = 0;
                }

                if ($perc >= $percentageInfo) {
                    $valueToLog = [];
                    foreach ($result as $optionId => $value) {
                        $valueToLog[] = $this->getAttributeOptionLabel($optionId, $comp);
                    }
                    $valueToLog = implode(', ', $valueToLog);

                    $resultData->setData((string) $this->getAttributeOption('factivity_' . $dbAttrName), $valueToLog);

                    return true;
                } else {
                    return false;
                }
            }

            $select = $this->resource->getConnection()
                                     ->select()
                                     ->from($table, ['option_id', 'attribute_id'])
                                     ->where('customer_id=?', $object->getId())
                                     ->where($fieldSearch . ' IS NOT NULL')
                                     ->where('option_id ' . $condition . '(?)', $option);

            if (stripos($dbAttrName, '_recent') !== false) {
                $select->where(
                    $fieldSearch . '_date  >= date_sub(?, INTERVAL ' . (int) $recentInfo . ' DAY)  ',
                    $this->pandaHelper->gmtDate()
                );
            }

            $result = $this->resource->getConnection()->fetchPairs($select);

            if ($result) {
                $valueToLog = [];
                foreach ($result as $optionId => $attributeId) {
                    $valueToLog[] = $this->getAttributeOptionLabel($optionId, $comp);
                }
                $valueToLog = implode(', ', $valueToLog);
            }

            if ($result) {
                $resultData->setData((string) $this->getAttributeOption('factivity_' . $dbAttrName), $valueToLog);

                return true;
            } else {
                return false;
            }
        }

        if (!$object->getData($this->getAttribute()) and
            in_array(
                $this->getAttribute(),
                [
                    'number_orders',
                    'number_reviews',
                    'order_amount',
                    'order_average',
                    'order_average_1year',
                    'order_amount_1year',
                    'order_average_older',
                    'order_amount_older',
                ]
            )
        ) {
            $object->setData($this->getAttribute(), 0);
        }

        $resultData->setData(
            (string) $this->getAttributeOption('factivity_' . $dbAttrName),
            $object->getData($this->getAttribute())
        );
        $resultData->setData('type_' . (string) $this->getAttributeOption('factivity_' . $dbAttrName), $dataT);

        return parent::validate($object);
    }

    /**
     * @return $this
     */
    public function collectValidatedAttributes()
    {

        $attribute = $this->getAttribute();

        $attributes = $this->getRule()->getCollectedAttributes();
        $attributes[$attribute] = true;
        $this->getRule()->setCollectedAttributes($attributes);

        return $this;
    }

    /**
     * @return string
     */
    public function translateOperator()
    {

        $operator = $this->getOperator();

        $newValue = [
            '=='  => '=',
            '!='  => '!=',
            '>='  => '>=',
            '<='  => '<=',
            '>'   => '>',
            '<'   => '<',
            '()'  => 'IN',
            '!()' => 'NOT IN',
        ];

        if (isset($newValue[$operator])) {
            return $newValue[$operator];
        }

        return '=';
    }

    /**
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {

        if (null === $this->_defaultOperatorInputByType) {
            parent::getDefaultOperatorInputByType();
            $this->_defaultOperatorInputByType['multiselect'] = ['()', '!()'];
            $this->_defaultOperatorInputByType['date'] = ['==', '>=', '<='];
            $this->_defaultOperatorInputByType['string'] = ['==', '!=', '()', '!()'];
        }

        return $this->_defaultOperatorInputByType;
    }

    /**
     * Retrieve Explicit Apply
     *
     * @return bool
     */
    public function getExplicitApply()
    {

        switch ($this->getAttribute()) {
            case 'factivity_visit_product':
            case 'factivity_visit_product_recent':
            case 'factivity_visit_product_freq':
            case 'factivity_visit_product_bought':
            case 'factivity_visit_product_bought_recent':
            case 'factivity_visit_product_bought_freq':
                return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getValueElementChooserUrl()
    {

        $url = false;
        switch ($this->getAttribute()) {
            case 'factivity_visit_product_bought':
            case 'factivity_visit_product_bought_recent':
            case 'factivity_visit_product_bought_freq':
                $url = 'adminhtml/promo_widget/chooser'
                       . '/attribute/sku';
                if ($this->getJsFormObject()) {
                    $url .= '/form/' . $this->getJsFormObject();
                }
                break;
        }

        return $url !== false ? $this->backendData->getUrl($url) : '';
    }

    /**
     * @return string
     */
    public function getValueAfterElementHtml()
    {

        $html = '';

        switch ($this->getAttribute()) {
            case 'factivity_visit_product_bought':
            case 'factivity_visit_product_bought_recent':
            case 'factivity_visit_product_bought_freq':
                $image = $this->_assetRepo->getUrl('images/rule_chooser_trigger.gif');
                break;
        }

        if (!empty($image)) {
            $html =
                '<a href="javascript:void(0)" class="rule-chooser-trigger"><img src="' . $image .
                '" alt="" class="v-middle rule-chooser-trigger" title="' . __('Open Chooser') .
                '" /></a>';
        }

        return $html;
    }

    /**
     * @param $optionId
     * @param $list
     *
     * @return mixed
     */
    public function getAttributeOptionLabel($optionId, $list)
    {

        $exists = (array) $this->registry->registry('panda_segments_attrs_labels');

        $attributeId = array_search($optionId, $list);

        if ($attributeId === false) {
            return 'N/A';
        }

        if (isset($exists[$attributeId][$optionId])) {
            return $exists[$attributeId][$optionId];
        }
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productFactory->create()
                                        ->setStoreId(0)
                                        ->setData($attributeId, $optionId);

        $label = $product->getAttributeText($attributeId);

        $exists[$attributeId][$optionId] = $label;

        $this->registry->register('panda_segments_attrs_labels', $exists, true);

        return $label;
    }

    /**
     * @param $categoryId
     *
     * @return mixed
     */
    public function getCategoryName($categoryId)
    {

        $exists = (array) $this->registry->registry('panda_segments_attrs_categories');

        if (isset($exists[$categoryId])) {
            return $exists[$categoryId];
        }

        $cat = $this->categoryFactory->create()
                                     ->load($categoryId)
                                     ->getName();

        $exists[$categoryId] = $cat;

        $this->registry->register('panda_segments_attrs_categories', $exists, true);

        return $cat;
    }
}
