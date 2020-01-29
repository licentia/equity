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
 * Class Product
 *
 * @package Licentia\Equity\Model\Segments\Condition
 */
class Product extends \Magento\Rule\Model\Condition\Product\AbstractProduct
{

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {

        $productAttributes = $this->_productResource->loadAllAttributes()->getAttributesByCode();

        $attributes = [];
        foreach ($productAttributes as $attribute) {
            /* @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
            if (!$attribute->isAllowedForRuleCondition() || !$attribute->getDataUsingMethod(
                    $this->_isUsedForRuleProperty
                )
            ) {
                continue;
            }
            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }

        $this->_addSpecialAttributes($attributes);

        $attributes['order_date'] = __('Order Date');

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * Add special attributes
     *
     * @param array $attributes
     *
     * @return void
     */
    protected function _addSpecialAttributes(array &$attributes)
    {

        parent::_addSpecialAttributes($attributes);
        $attributes['quote_item_qty'] = __('Quantity in cart');
        $attributes['quote_item_price'] = __('Price in cart');
        $attributes['quote_item_row_total'] = __('Row total in cart');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $model
     *
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        /** @var \Magento\Sales\Model\Order\Item $model */

        /** @var \Magento\Catalog\Model\Product $product */
        $attrCode = $this->getAttribute();

        if ($attrCode == 'order_date') {
            $order = $objectManager->create('\Magento\Sales\Model\OrderFactory')->load($model->getOrderId());
            $this->setValue(substr($order->getCreatedAt(), 0, 10));
        }

        if ($attrCode == 'order_acquisition_campaign') {
            $order = $objectManager->create('\Magento\Sales\Model\OrderFactory')->load($model->getOrderId());
            $this->setValue($order->getData('panda_acquisition_campaign'));
        }

        $product = $model->getProduct();
        if (!$product instanceof \Magento\Catalog\Model\Product) {
            try {
                $product = $this->productRepository->get($model->getSku());
            } catch (\Exception $e) {
                return false;
            }
        }

        $product->setQuoteItemQty($model->getQtyInvoiced())
                ->setQuoteItemPrice($model->getBasePrice())
                ->setQuoteItemRowTotal($model->getBaseRowTotal());

        if ('category_ids' == $attrCode) {
            return $this->validateAttribute($this->_getAvailableInCategories($product->getId()));
        }

        return parent::validate($product);
    }

    /**
     * @return string
     */
    public function getInputType()
    {

        if ($this->getAttribute() == 'order_date') {
            return 'date';
        }

        return parent::getInputType();
    }

    /**
     * Retrieve value element type
     *
     * @return string
     */
    public function getValueElementType()
    {

        if ($this->getAttribute() == 'order_date') {
            return 'date';
        }

        return parent::getValueElementType();
    }

    /**
     * Retrieve value element chooser URL
     *
     * @return string
     */
    public function getValueElementChooserUrl()
    {

        $url = false;
        switch ($this->getAttribute()) {
            case 'sku':
            case 'category_ids':
                $url = 'sales_rule/promo_widget/chooser/attribute/' . $this->getAttribute();
                if ($this->getJsFormObject()) {
                    $url .= '/form/' . $this->getJsFormObject();
                }
                break;
            default:
                break;
        }

        return $url !== false ? $this->_backendData->getUrl($url) : '';
    }

    /**
     * @return bool
     */
    public function getExplicitApply()
    {

        if ($this->getAttribute() == 'order_date') {
            return true;
        }

        return parent::getExplicitApply();
    }
}
