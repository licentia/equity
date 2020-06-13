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

/**
 * Class Search
 *
 * @package Licentia\Equity\Model\Segments\Condition
 */
class Search extends AbstractCondition
{

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {

        $attributes = [
            'searched'        => __('Searched For...'),
            'searched_recent' => __('Searched For... Recently'),
            'searched_freq'   => __('Searched for... Frequently'),
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

        $recentInfo = (int) $this->scopeConfig->getValue(
            'panda_magna/segments/recent',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
        if ($recentInfo < 1) {
            $recentInfo = 21;
        }

        $percentageInfo = $this->scopeConfig->getValue(
            'panda_magna/segments/percentage',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );

        $dbAttrName = $this->getAttribute();
        $resultData = $this->registry->registry('panda_segments_data');

        $condition = $this->translateOperator();

        if (stripos($condition, 'IN') !== false) {
            $values = str_getcsv($this->getValue());
            $values = array_map('trim', $values);
            $this->setValue($values);
        }

        if ($this->getOperator() == '{}') {
            $this->setValue('%' . $this->getValue() . '%');
        }

        $valueToLog = $this->getValue();
        if (is_array($this->getValue())) {
            $valueToLog = implode(',', $this->getValue());
        }

        $negate = false;
        if (substr($this->getOperator(), 0, 1) == '!') {
            $negate = true;
        }

        $table = $this->resource->getTable('panda_segments_metadata_searches');

        if (stripos($dbAttrName, '_freq') !== false) {
            $select = $this->resource->getConnection()
                                     ->select()
                                     ->from($table, ['query', 'total' => new \Zend_Db_Expr('COUNT(*)')])
                                     ->group('query')
                                     ->where('email=?', $object->getEmail());

            if (in_array($this->translateOperator(), ['IN', 'NOT IN'])) {
                $select->where(' query (' . $condition . ')', $this->getValue());
            } else {
                $select->where(' query ' . $condition, $this->getValue());
            }

            $result = $this->resource->getConnection()->fetchPairs($select);

            if (!$result) {
                return !$negate;
            }

            $total = array_sum($result);
            $user = array_sum(array_intersect_key($result, array_flip((array) $this->getValue())));
            if ($total > 0) {
                $perc = round($user * 100 / $total);
            } else {
                $perc = 0;
            }

            if ($perc >= $percentageInfo) {
                if ($resultData) {
                    $resultData->setData((string) $this->getAttribute(), (string) $valueToLog);
                }

                return !$negate;
            } else {
                return !$negate;
            }
        }

        $select = $this->resource->getConnection()
                                 ->select()
                                 ->from($table, ['query', 'total' => new \Zend_Db_Expr('COUNT(*)')])
                                 ->group('query')
                                 ->where(' query ' . $condition, $this->getValue())
                                 ->where('email=?', $object->getEmail());

        if (stripos($dbAttrName, '_recent') !== false) {
            $select->where(
                'created_at >= date_sub(?, INTERVAL ' . (int) $recentInfo . ' DAY)  ',
                $this->pandaHelper->gmtDate()
            );
        }

        $result = $this->resource->getConnection()->fetchRow($select);

        if ($result) {
            if ($resultData) {
                $resultData->setData((string) $this->getAttribute(), $valueToLog);
            }

            return !$negate;
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
     * @return string
     */
    public function translateOperator()
    {

        $operator = $this->getOperator();

        $newValue = [
            '=='  => '= ?',
            '!='  => '!= ?',
            '{}'  => 'LIKE ? ',
            '!{}' => 'NOT LIKE ? ',
            '()'  => 'IN ?',
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
            $this->_defaultOperatorInputByType['string'] = ['==', '{}', '!=', '!{}', '()'];
        }

        return $this->_defaultOperatorInputByType;
    }
}
