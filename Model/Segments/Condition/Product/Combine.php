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

namespace Licentia\Equity\Model\Segments\Condition\Product;

use Magento\Catalog\Model\ResourceModel\Product\Collection;

/**
 * Class Combine
 *
 * @package Licentia\Equity\Model\Segments\Condition\Product
 */
class Combine extends \Magento\Rule\Model\Condition\Combine
{

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\Product
     */
    protected $ruleConditionProduct;

    /**
     * @param \Magento\Rule\Model\Condition\Context           $context
     * @param \Magento\SalesRule\Model\Rule\Condition\Product $ruleConditionProduct
     * @param array                                           $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\SalesRule\Model\Rule\Condition\Product $ruleConditionProduct,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->ruleConditionProduct = $ruleConditionProduct;
        $this->setType(\Magento\SalesRule\Model\Rule\Condition\Product\Combine::class);
    }

    /**
     * Get new child select options
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {

        $productAttributes = $this->ruleConditionProduct->loadAttributeOptions()->getAttributeOption();
        $pAttributes = [];
        $iAttributes = [];
        $oAttributes = [];
        foreach ($productAttributes as $code => $label) {
            if (strpos($code, 'quote_item_') === 0) {
                $iAttributes[] = [
                    'value' => \Licentia\Equity\Model\Segments\Condition\Product::class . '|' . $code,
                    'label' => $label,
                ];
            } else {
                $pAttributes[] = [
                    'value' => \Licentia\Equity\Model\Segments\Condition\Product::class . '|' . $code,
                    'label' => $label,
                ];
            }
        }

        $oAttributes[] = [
            'value' => \Licentia\Equity\Model\Segments\Condition\Product::class . '|order_date',
            'label' => 'Order Date',
        ];
        $oAttributes[] = [
            'value' => \Licentia\Equity\Model\Segments\Condition\Product::class . '|order_acquisition_campaign',
            'label' => 'Order Acquisition Campaign',
        ];

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            [
                ['label' => __('Order Attribute'), 'value' => $oAttributes],
                ['label' => __('Cart Item Attribute'), 'value' => $iAttributes],
                ['label' => __('Product Attribute'), 'value' => $pAttributes],
            ]
        );

        return $conditions;
    }

    /**
     * Collect validated attributes
     *
     * @param Collection $productCollection
     *
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {

        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }

        return $this;
    }
}
