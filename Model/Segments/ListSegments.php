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

namespace Licentia\Equity\Model\Segments;

/**
 * Class ListSegments
 *
 * @package Licentia\Equity\Model\Segments
 */
class ListSegments extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @var \Licentia\Equity\Model\ResourceModel\Segments\ListSegments\CollectionFactory
     */
    protected \Licentia\Equity\Model\ResourceModel\Segments\ListSegments\CollectionFactory $listSegmentsCollection;

    /**
     * @var \Licentia\Equity\Model\SegmentsFactory
     */
    protected \Licentia\Equity\Model\SegmentsFactory $segmentsFactory;

    protected \Magento\Customer\Model\CustomerFactory $customerFactory;

    protected \Licentia\Panda\Model\SubscribersFactory $subscribersFactory;

    /**
     * @param \Licentia\Panda\Model\SubscribersFactory                                     $subscribersFactory
     * @param \Licentia\Equity\Model\SegmentsFactory                                       $segmentsFactory
     * @param \Licentia\Equity\Model\ResourceModel\Segments\ListSegments\CollectionFactory $listSegmentsCollection
     * @param \Magento\Customer\Model\CustomerFactory                                      $customerFactory
     * @param \Magento\Framework\Model\Context                                             $context
     * @param \Magento\Framework\Registry                                                  $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null                 $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null                           $resourceCollection
     * @param array                                                                        $data
     */
    public function __construct(
        \Licentia\Panda\Model\SubscribersFactory $subscribersFactory,
        \Licentia\Equity\Model\SegmentsFactory $segmentsFactory,
        \Licentia\Equity\Model\ResourceModel\Segments\ListSegments\CollectionFactory $listSegmentsCollection,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Model\Context $context,
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

        $this->subscribersFactory = $subscribersFactory;
        $this->segmentsFactory = $segmentsFactory;
        $this->listSegmentsCollection = $listSegmentsCollection;
        $this->customerFactory = $customerFactory;
    }

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected string $_eventPrefix = 'panda_segments_records';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(\Licentia\Equity\Model\ResourceModel\Segments\ListSegments::class);
    }

    /**
     * @param $customerId
     *
     * @return mixed
     */
    public function getCustomerSegments($customerId)
    {

        return $this->listSegmentsCollection->create()->addFieldToFilter('customer_id', $customerId);
    }

    /**
     * @param $subscriberId
     *
     * @return mixed
     */
    public function getSubscriberSegments($subscriberId)
    {

        return $this->listSegmentsCollection->create()->addFieldToFilter('subscriber_id', $subscriberId);
    }

    /**
     * @param        $identifier
     * @param        $segmentId
     * @param string $field
     *
     * @return $this|bool
     * @throws \Exception
     */
    public function addRecordToSegment($identifier, $segmentId, $field = 'customer_id')
    {

        if ($field === 'subscriber_id') {
            $model = $this->subscribersFactory->create()->load($identifier);
            $customerId = null;
            $subscriberId = $identifier;
        } else {
            $model = $this->customerFactory->create()->load($identifier);
            $customerId = $identifier;
            $subscriberId = null;
        }

        if (!$model->getId()) {
            return false;
        }

        $item = $this->listSegmentsCollection->create()
                                             ->addFieldToFilter($field, $identifier)
                                             ->addFieldToFilter('segment_id', $segmentId);

        if ($item->count() == 0) {
            $data = [];
            /** @var \Licentia\Equity\Model\Segments $segment */
            $segment = $this->segmentsFactory->create()->load($segmentId);
            $data['manual'] = '1';
            $data['customer_id'] = $customerId;
            $data['segment_id'] = $segmentId;
            $data['customer_name'] = $model->getName();
            $data['subscriber_id'] = $subscriberId;
            $data['email'] = $model->getEmail();
            try {
                $record = $this->setData($data)
                               ->save();
                $segment->setRecords($segment->getRecords() + 1)
                        ->setManuallyAdded($segment->getManuallyAdded() + 1)
                        ->save();
            } catch (\Exception $e) {
                $record = false;
            }

            return $record;
        }

        if ($item->getFirstItem()->getData('manual') == 0) {

            $segment = $this->segmentsFactory->create()->load($segmentId);
            $segment->setManuallyAdded($segment->getManuallyAdded() + 1)
                    ->save();

            return $item->getFirstItem()
                        ->setData('manual', 1)
                        ->save();
        }

        return false;
    }

    /**
     * @param        $identifier
     * @param        $segmentId
     * @param string $field
     *
     * @return $this|bool
     */
    public function removeRecordFromSegment($identifier, $segmentId, $field = 'customer_id')
    {

        $item = $this->listSegmentsCollection->create()
                                             ->addFieldToFilter($field, $identifier)
                                             ->addFieldToFilter('segment_id', $segmentId);

        if ($item->count() > 0) {
            $segment = $this->segmentsFactory->create()->load($segmentId);

            try {
                /** @var ListSegments $record */
                foreach ($item as $record) {
                    $record->delete();
                    $segment->setData('records', $segment->getData('records') - 1)
                            ->setData('manually_added', $segment->getData('manually_added') - 1)
                            ->save();
                }
            } catch (\Exception $e) {
            }

            return true;
        }

        return false;
    }

    /**
     * @param $subscriberId
     */
    public function clearSubscriberSegments($subscriberId): void
    {

        $ids = $this->getSubscriberSegments($subscriberId)->toOptionHash();

        foreach ($ids as $id) {
            $this->removeRecordFromSegment($subscriberId, $id, 'subscriber_id');
        }
    }

    /**
     * @param $id
     *
     * @return $this|bool
     * @throws \Exception
     */
    public function changeToManual($id)
    {

        $item = $this->load($id);

        if (!$item->getId()) {
            return false;
        }

        if ($item->getData('manual') == 0) {
            $segment = $this->segmentsFactory->create()->load($item->getSegmentId());
            $segment->setData('manually_added', $segment->getData('manually_added') + 1)
                    ->save();

            return $item->setData('manual', 1)
                        ->save();
        }

        return false;
    }

    /**
     * @param $data
     *
     * @return $this
     * @throws \Exception
     */
    public function saveRecord($data): ListSegments
    {

        $item = $this->listSegmentsCollection->create()
                                             ->addFieldToFilter('customer_id', $data['customer_id'])
                                             ->addFieldToFilter('segment_id', $data['segment_id'])
                                             ->setPageSize(1);
        $data['revert'] = 0;
        if ($item->getSize() == 0) {
            return $this->setData($data)
                        ->save();
        }

        return $item->getFirstItem()
                    ->addData($data)
                    ->save();
    }

    /**
     * @param $id
     *
     * @return $this|bool
     * @throws \Exception
     */
    public function changeToAuto($id)
    {

        $item = $this->load($id);

        if (!$item->getId()) {
            return false;
        }

        if ($item->getData('manual') == 1) {
            $segment = $this->segmentsFactory->create()->load($item->getSegmentId());
            $segment->setData('manually_added', $segment->getData('manually_added') - 1)
                    ->save();

            return $item->setData('manual', 0)
                        ->save();
        }

        return false;
    }

    /**
     * @param $recordId
     *
     * @return $this
     */
    public function setRecordId($recordId)
    {

        return $this->setData('record_id', $recordId);
    }

    /**
     * @param $segmentId
     *
     * @return $this
     */
    public function setSegmentId($segmentId): ListSegments
    {

        return $this->setData('segment_id', $segmentId);
    }

    /**
     * @param $customerName
     *
     * @return $this
     */
    public function setCustomerName($customerName)
    {

        return $this->setData('customer_name', $customerName);
    }

    /**
     * @param $email
     *
     * @return $this
     */
    public function setEmail($email)
    {

        return $this->setData('email', $email);
    }

    /**
     * @param $customerId
     *
     * @return $this
     */
    public function setCustomerId($customerId)
    {

        return $this->setData('customer_id', $customerId);
    }

    /**
     * @param $subscriberId
     *
     * @return $this
     */
    public function setSubscriberId($subscriberId)
    {

        return $this->setData('subscriber_id', $subscriberId);
    }

    /**
     * @param $data1
     *
     * @return $this
     */
    public function setData1($data1)
    {

        return $this->setData('data1', $data1);
    }

    /**
     * @param $data_2
     *
     * @return $this
     */
    public function setData2($data_2)
    {

        return $this->setData('data_2', $data_2);
    }

    /**
     * @param $data_3
     *
     * @return $this
     */
    public function setData3($data_3)
    {

        return $this->setData('data_3', $data_3);
    }

    /**
     * @param $data_4
     *
     * @return $this
     */
    public function setData4($data_4)
    {

        return $this->setData('data_4', $data_4);
    }

    /**
     * @param $data_5
     *
     * @return $this
     */
    public function setData5($data_5)
    {

        return $this->setData('data_5', $data_5);
    }

    /**
     * @param $data_6
     *
     * @return $this
     */
    public function setData6($data_6)
    {

        return $this->setData('data_6', $data_6);
    }

    /**
     * @param $data_7
     *
     * @return $this
     */
    public function setData7($data_7)
    {

        return $this->setData('data_7', $data_7);
    }

    /**
     * @param $data_8
     *
     * @return $this
     */
    public function setData8($data_8)
    {

        return $this->setData('data_8', $data_8);
    }

    /**
     * @param $data_9
     *
     * @return $this
     */
    public function setData9($data_9)
    {

        return $this->setData('data_9', $data_9);
    }

    /**
     * @param $data_10
     *
     * @return $this
     */
    public function setData10($data_10)
    {

        return $this->setData('data_10', $data_10);
    }

    /**
     * @param $manual
     *
     * @return $this
     */
    public function setManual($manual)
    {

        return $this->setData('manual', $manual);
    }

    /**
     * @param $revert
     *
     * @return $this
     */
    public function setRevert($revert)
    {

        return $this->setData('revert', $revert);
    }

    /**
     * @return mixed
     */
    public function getRecordId()
    {

        return $this->getData('record_id');
    }

    /**
     * @return mixed
     */
    public function getSegmentId()
    {

        return $this->getData('segment_id');
    }

    /**
     * @return mixed
     */
    public function getCustomerName()
    {

        return $this->getData('customer_name');
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {

        return $this->getData('email');
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {

        return $this->getData('customer_id');
    }

    /**
     * @return mixed
     */
    public function getSubscriberId()
    {

        return $this->getData('subscriber_id');
    }

    /**
     * @return mixed
     */
    public function getData1()
    {

        return $this->getData('data_1');
    }

    /**
     * @return mixed
     */
    public function getData2()
    {

        return $this->getData('data_2');
    }

    /**
     * @return mixed
     */
    public function getData3()
    {

        return $this->getData('data_3');
    }

    /**
     * @return mixed
     */
    public function getData4()
    {

        return $this->getData('data_4');
    }

    /**
     * @return mixed
     */
    public function getData5()
    {

        return $this->getData('data_5');
    }

    /**
     * @return mixed
     */
    public function getData6()
    {

        return $this->getData('data_6');
    }

    /**
     * @return mixed
     */
    public function getData7()
    {

        return $this->getData('data_7');
    }

    /**
     * @return mixed
     */
    public function getData8()
    {

        return $this->getData('data_8');
    }

    /**
     * @return mixed
     */
    public function getData9()
    {

        return $this->getData('data_9');
    }

    /**
     * @return mixed
     */
    public function getData10()
    {

        return $this->getData('data_10');
    }

    /**
     * @return mixed
     */
    public function getManual()
    {

        return $this->getData('manual');
    }

    /**
     * @return mixed
     */
    public function getRevert()
    {

        return $this->getData('revert');
    }
}
