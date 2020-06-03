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

namespace Licentia\Equity\Model\Segments\Condition\Product;

/**
 * Class Found
 *
 * @package Licentia\Equity\Model\Segments\Condition\Product
 */
class Found extends Combine
{

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollection;

    /**
     * Found constructor.
     *
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory
     * @param \Magento\Rule\Model\Condition\Context                      $context
     * @param \Magento\SalesRule\Model\Rule\Condition\Product            $ruleConditionProduct
     * @param array                                                      $data
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\SalesRule\Model\Rule\Condition\Product $ruleConditionProduct,
        array $data = []
    ) {

        parent::__construct($context, $ruleConditionProduct, $data);

        $this->setType(Found::class);

        $this->orderCollection = $collectionFactory;
    }

    /**
     * Load value options
     *
     * @return $this
     */
    public function loadValueOptions()
    {

        $this->setValueOption([1 => __('FOUND'), 0 => __('NOT FOUND')]);

        return $this;
    }

    /**
     * Return as html
     *
     * @return string
     */
    public function asHtml()
    {

        $html = $this->getTypeElement()->getHtml() . __(
                "If an item is %1 in a previous order cart with %2 of these conditions true:",
                $this->getValueElement()->getHtml(),
                $this->getAggregatorElement()->getHtml()
            );
        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }

        return $html;
    }

    /**
     * Validate
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     *
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {

        /** @var \Magento\Customer\Model\Customer $model */
        $all = $this->getAggregator() === 'all';
        $true = (bool) $this->getValue();

        $orders = $this->orderCollection->create();

        if ($model->getCustomerId()) {
            $orders->getSelect()->where(
                'customer_email=? OR customer_id=' . $model->getCustomerId(),
                $model->getEmail()
            );
        } else {
            $orders->getSelect()->where('customer_email=? ', $model->getEmail());
        }

        /** @var \Magento\Sales\Model\Order $order */
        foreach ($orders as $order) {
            try {
                $found = false;
                foreach ($order->getAllItems() as $item) {
                    $found = $all;
                    foreach ($this->getConditions() as $cond) {
                        $validated = $cond->validate($item);
                        if ($all && !$validated || !$all && $validated) {
                            $found = $validated;
                            break;
                        }
                    }
                    if ($found && $true || !$true && $found) {
                        break;
                    }
                }
                // found an item and we're looking for existing one
                if ($found && $true) {
                    return true;
                } elseif (!$found && !$true) {
                    // not found and we're making sure it doesn't exist
                    return true;
                }
            } catch (\Exception $e) {
            }
        }

        return false;
    }
}
