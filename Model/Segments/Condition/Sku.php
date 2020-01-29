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

namespace Licentia\Equity\Model\Segments\Condition;

/**
 * Class Sku
 *
 * @package Licentia\Equity\Model\Segments\Condition
 */
class Sku extends \Magento\CatalogRule\Model\Rule\Condition\Product
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Sku constructor.
     *
     * @param \Magento\Framework\Registry                                      $registry
     * @param \Magento\Rule\Model\Condition\Context                            $context
     * @param \Magento\Backend\Helper\Data                                     $backendData
     * @param \Magento\Eav\Model\Config                                        $config
     * @param \Magento\Catalog\Model\ProductFactory                            $productFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface                  $productRepository
     * @param \Magento\Catalog\Model\ResourceModel\Product                     $productResource
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection
     * @param \Magento\Framework\Locale\FormatInterface                        $localeFormat
     * @param array                                                            $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Eav\Model\Config $config,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        array $data = []
    ) {

        $this->registry = $registry;
        parent::__construct(
            $context,
            $backendData,
            $config,
            $productFactory,
            $productRepository,
            $productResource,
            $attrSetCollection,
            $localeFormat,
            $data
        );
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {

        $attributes = [
            'sku' => __('Product SKU'),
        ];

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $object)
    {

        $dbAttrName = $this->getAttribute();
        $currentSegment = $this->registry->registry('panda_segment');

        $parsed = $this->getValueParsed();
        $resource = $this->_productResource->getConnection();
        $ordersTable = $resource->getTableName('sales_order');
        $ordersItemTable = $resource->getTableName('sales_order_item');
        $productsTable = $resource->getTableName('catalog_product_entity');

        $select = $resource->select()
                           ->from($ordersTable, [])
                           ->join(
                               $ordersItemTable,
                               $ordersItemTable . '.order_id = ' . $ordersTable . '.entity_id',
                               []
                           )
                           ->join(
                               $productsTable,
                               $productsTable . '.entity_id = ' . $ordersItemTable . '.product_id',
                               ['sku']
                           );

        if ($object->getData('customer_id') > 0) {
            $select->where($ordersTable . '.customer_id=?', $object->getCustomerId());
        } else {
            $select->where($ordersTable . '.customer_email=?', $object->getEmail());
        }

        if (stripos($this->translateOperator(), 'like') !== false) {
            $parsed = '%' . $parsed . '%';
        }

        $closed = $select->getAdapter()->quote(\Magento\Sales\Model\Order::STATE_CLOSED);
        $complete = \Magento\Sales\Model\Order::STATE_COMPLETE;

        $select->where($ordersTable . '.state=? OR ' . $ordersTable . '.state=' . $closed, $complete);

        if ($currentSegment) {
            $select->where($ordersTable . '.store_id IN(?)', $currentSegment->getStoreIds());
        }

        if (in_array($this->translateOperator(), ['IN', 'NOT IN'])) {
            $select->where($productsTable . '.' . $dbAttrName . ' ' . $this->translateOperator() . ' (?) ', $parsed);
        } else {
            $select->where($productsTable . '.' . $dbAttrName . ' ' . $this->translateOperator() . ' ? ', $parsed);
        }

        $result = $resource->fetchCol($select);

        $logResult = is_array($this->getValueParsed()) ? '--N/A--' : $this->getValueParsed();
        $resultData = $this->registry->registry('panda_segments_data');

        $resultData->setData((string) $this->getAttributeName(), $logResult);

        if (count($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     *
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {

        $attribute = $this->getAttribute();
        $attributes = $this->getRule()->getCollectedAttributes();
        $attributes[$attribute] = true;
        $this->getRule()->setCollectedAttributes($attributes);

        return $this;
    }

    /**
     * @return mixed|string
     */
    /**
     * @return mixed|string
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
}
