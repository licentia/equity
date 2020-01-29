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

namespace Licentia\Equity\Model;

/**
 * Class Access
 *
 * @package Licentia\Panda\Model
 */
class Access extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_segments_access';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'segments_access';

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Licentia\Equity\Model\ResourceModel\Access\CollectionFactory
     */
    protected $accessCollection;

    /**
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Licentia\Equity\Helper\Data                                 $pandaHelper
     * @param ResourceModel\Access\CollectionFactory                       $accessCollection
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Licentia\Equity\Helper\Data $pandaHelper,
        \Licentia\Equity\Model\ResourceModel\Access\CollectionFactory $accessCollection,
        \Magento\Framework\Registry $registry,
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

        $this->pandaHelper = $pandaHelper;
        $this->accessCollection = $accessCollection;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(\Licentia\Equity\Model\ResourceModel\Access::class);
    }

    /**
     * @return array
     */
    public static function getAccessTypes()
    {

        return [
            'category' => __('Category'),
            'product'  => __('Product'),
            'page'     => __('CMS Page'),
            'block'    => __('CMS Block'),
        ];
    }

    /**
     * @param $entityId
     * @param $entityType
     *
     * @return bool
     */
    public function checkAccess($entityId, $entityType)
    {

        $now = $this->pandaHelper->gmtDate();

        $collection = $this->accessCollection->create()
                                             ->addFieldToFilter('is_active', 1)
                                             ->addFieldToFilter('to_date', ['gteq' => $now])
                                             ->addFieldToFilter('from_date', ['lteq' => $now])
                                             ->addFieldToFilter('entity_type', $entityType)
                                             ->addFieldToFilter('entity_id', $entityId);

        $totalModel = $collection->getAllIds('segments_ids');

        if (count($totalModel) == 0) {
            return true;
        }

        $totalModel = implode(',', $totalModel);
        $totalModel = explode(',', $totalModel);
        $totalModel = array_filter($totalModel);

        $customerSegments = $this->pandaHelper->getCustomerSegmentsIds();

        if (count(array_intersect($totalModel, $customerSegments)) > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param \Magento\Framework\Data\Collection\AbstractDb $collection
     * @param                                               $entityType
     *
     * @return bool|\Magento\Framework\Data\Collection\AbstractDb
     */
    public function getLockedEntities(\Magento\Framework\Data\Collection\AbstractDb $collection, $entityType)
    {

        $now = $this->pandaHelper->gmtDate();

        $access = $this->accessCollection->create()
                                         ->addFieldToFilter('is_active', 1)
                                         ->addFieldToFilter('to_date', ['gteq' => $now])
                                         ->addFieldToFilter('from_date', ['lteq' => $now])
                                         ->addFieldToFilter('entity_type', $entityType);

        if ($access->getSize() == 0) {
            return true;
        }

        $customerSegments = $this->pandaHelper->getCustomerSegmentsIds();

        $ids = [];

        /** @var \Licentia\Equity\Model\Access $item */
        foreach ($access as $item) {
            $segs = explode(',', $item->getSegmentsIds());

            $final = array_diff($segs, $customerSegments);

            if ($final) {
                $ids[] = $item->getEntityId();
            }
        }

        $parts = $collection->getSelect()->getPart('from');

        if (isset($parts['e']['tableName']) &&
            isset($parts['e']['tableName']) == $collection->getResource()->getTable('catalog_product_entity')) {
            $collection->getSelect()->where('e.entity_id NOT IN (?)', $ids);
        }

        return $collection;
    }

    /**
     * @param $accessId
     *
     * @return $this
     */
    public function setAccessId($accessId)
    {

        return $this->setData('access_id', $accessId);
    }

    /**
     * @param $entityType
     *
     * @return $this
     */
    public function setEntityType($entityType)
    {

        return $this->setData('entity_type', $entityType);
    }

    /**
     * @param $entityId
     *
     * @return $this
     */
    public function setEntityId($entityId)
    {

        return $this->setData('entity_id', $entityId);
    }

    /**
     * @param $segmentsIds
     *
     * @return $this
     */
    public function setSegmentsIds($segmentsIds)
    {

        return $this->setData('segments_ids', $segmentsIds);
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {

        return $this->setData('name', $name);
    }

    /**
     * @param $fromDate
     *
     * @return $this
     */
    public function setFromDate($fromDate)
    {

        return $this->setData('from_date', $fromDate);
    }

    /**
     * @param $toDate
     *
     * @return $this
     */
    public function setToDate($toDate)
    {

        return $this->setData('to_date', $toDate);
    }

    /**
     * @param $isActive
     *
     * @return $this
     */
    public function setIsActive($isActive)
    {

        return $this->setData('is_active', $isActive);
    }

    /**
     * @return mixed
     */
    public function getAccessId()
    {

        return $this->getData('access_id');
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {

        return $this->getData('entity_type');
    }

    /**
     * @return mixed
     */
    public function getEntityId()
    {

        return $this->getData('entity_id');
    }

    /**
     * @return mixed
     */
    public function getSegmentsIds()
    {

        return $this->getData('segments_ids');
    }

    /**
     * @return mixed
     */
    public function getName()
    {

        return $this->getData('name');
    }

    /**
     * @return mixed
     */
    public function getFromDate()
    {

        return $this->getData('from_date');
    }

    /**
     * @return mixed
     */
    public function getToDate()
    {

        return $this->getData('to_date');
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {

        return $this->getData('is_active');
    }
}
