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

namespace Licentia\Equity\Model;

/**
 * Class TwoFactor
 *
 * @package Licentia\Panda\Model
 */
class TwoFactor extends \Magento\Framework\Model\AbstractModel
{

    const REMINDER_COOKIE_NAME = 'panda_auth';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_two_factor';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'panda_two_factor';

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\TwoFactor::class);
    }

    /**
     * TwoFactor constructor.
     *
     * @param \Magento\Framework\UrlInterface                              $url
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
     * @param \Magento\Customer\Model\Session                              $customerSession
     * @param \Licentia\Equity\Helper\Data                                 $helperData
     * @param \Magento\Framework\App\RequestInterface                      $request
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $scopeConfig
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Licentia\Equity\Helper\Data $helperData,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->url = $url;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->pandaHelper = $helperData;
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $event
     */
    public function logoutCustomer($event)
    {

        if (!$this->scopeConfig->isSetFlag('panda_customer/twofactor/enabled')) {
            return;
        }
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $event->getCustomer();

        $item = $this->getCollection()
                     ->addFieldToFilter('remember_hash', $this->pandaHelper->getTwoAuthRememberCode())
                     ->addFieldToFilter('customer_id', $customer->getId());

        /** @var TwoFactor $log */
        foreach ($item as $log) {
            $log->setRememberHash('')
                ->save();
        }

    }

    /**
     * @return bool
     */
    public function validateRemember()
    {

        $hash = $this->pandaHelper->getTwoAuthRememberCode();

        if (!isset($_COOKIE[self::REMINDER_COOKIE_NAME]) || $_COOKIE[self::REMINDER_COOKIE_NAME] != $hash) {
            return false;
        }

        $item = $this->getCollection()
                     ->addFieldToFilter('used', 1)
                     ->addFieldToFilter('remember_until', ['gteq' => $this->pandaHelper->gmtDate()])
                     ->addFieldToFilter('remember_hash', $hash)
                     ->addFieldToFilter('customer_id', $this->customerSession->getCustomerId())
                     ->getFirstItem();

        return $item->getId() ? true : false;

    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     *
     * @return bool
     */
    public function checkLogin(\Magento\Framework\Event\Observer $event)
    {

        if (!$this->scopeConfig->isSetFlag('panda_customer/twofactor/enabled')) {
            return true;
        }

        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $event->getCustomer();

        $data = $this->scopeConfig->getValue(
            'panda_customer/twofactor',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $groups = explode(',', $data['customer_groups']);
        $segments = explode(',', $data['segments']);

        if (isset($data['allow_remember']) && $data['allow_remember'] == 1) {
            if ($this->validateRemember()) {
                return true;
            }
        }

        if (in_array($customer->getGroupId(), $groups) ||
            array_intersect($segments, $this->pandaHelper->getCustomerSegmentsIds())) {
            $this->customerSession->setData('panda_twofactor_required', true);

            try {

                $this->generateCode($this->customerSession->getCustomer());
            } catch (\Exception $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     *
     * @return bool|int
     */
    public function canGenerateCode(\Magento\Customer\Model\Customer $customer)
    {

        $data = $this->scopeConfig->getValue(
            'panda_customer/twofactor',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $resource = $this->getResource();
        $connection = $resource->getConnection();

        $currentDate = new \DateTime($this->pandaHelper->gmtDate());
        $currentDate->sub(new \DateInterval('PT1H'));
        $hourDate = $currentDate->format('Y-m-d H:i:s');

        $attemptsHour = $connection->fetchOne(
            $connection->select()
                       ->from($resource->getTable('panda_two_factor_attempts'), ['COUNT(*)'])
                       ->where('customer_id=?', $customer->getId())
                       ->where("attempt_date>=?", $hourDate)
        );

        if ($attemptsHour >= $data['max_resends']) {
            return -2;
        }

        $currentDate = new \DateTime($this->pandaHelper->gmtDate());
        $currentDate->sub(new \DateInterval('P1D'));
        $dayDate = $currentDate->format('Y-m-d H:i:s');

        $attemptsDay = $connection->fetchOne(
            $connection->select()
                       ->from($resource->getTable('panda_two_factor_attempts'), ['COUNT(*)'])
                       ->where('customer_id=?', $customer->getId())
                       ->where("attempt_date>=?", $dayDate)
        );

        if ($attemptsDay >= $data['max_resends_day']) {
            return -1;
        }

        return true;
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     *
     * @return bool|TwoFactor
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generateCode(\Magento\Customer\Model\Customer $customer)
    {

        if (!$customer->getData('panda_twofactor_number')) {

            $customer->load($customer->getId())->getData();

            if (!$customer->getData('panda_twofactor_number')) {
                return false;
            }
        }

        if (!$this->canGenerateCode($customer)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Too many requests'));
        }

        $data = $this->scopeConfig->getValue(
            'panda_customer/twofactor',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $i = 1;
        $code = '';
        while (true) {
            $code = str_pad(rand(0, pow(10, $data['length']) - 1), $data['length'], '0', STR_PAD_LEFT);

            $collection = $this->getCollection()
                               ->addFieldToFilter('used', 0)
                               ->addFieldToFilter('is_active', 1)
                               ->addFieldToFilter('code', $code);

            if ($collection->count() == 0) {
                break;
            }

            if ($i > 100) {
                return false;
            }

            $i++;
        }

        if (stripos($data['message'], '{code}') === false) {
            $data['message'] .= ' {code}';
        }

        $message = str_replace('{code}', $code, __($data['message']));

        $phone = $customer->getData('panda_twofactor_number');

        $send = $this->pandaHelper->getSmsTransport($data['sender'])->sendSMS($phone, $message);

        if ($send) {
            $new = [];
            $new['customer_id'] = $customer->getId();
            $new['customer_name'] = $customer->getName();
            $new['customer_email'] = $customer->getEmail();
            $new['phone'] = $phone;
            $new['code'] = $code;
            $new['message'] = $message;
            $new['used'] = 0;
            $new['sent_at'] = $this->pandaHelper->gmtDate();
            $new['is_active'] = 1;
            $new['store_id'] = $this->storeManager->getStore()->getId();

            $collection = $this->getCollection()
                               ->addFieldToFilter('customer_id', $customer->getId())
                               ->addFieldToFilter('is_active', 1);

            /** @var TwoFactor $item */
            foreach ($collection as $item) {
                $item->setIsActive(0)->setUsed(0)->save();
            }

            return $this->setData([])->setData($new)->save();
        } else {
            throw  new \Magento\Framework\Exception\LocalizedException(__('Error Sending SMS'));
        }

        return false;
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @param                                  $code
     * @param                                  $remember
     * @param                                  $hash
     *
     * @return bool
     */
    public function validateCode(
        \Magento\Customer\Model\Customer $customer,
        $code,
        $remember,
        $hash
    ) {

        $currentDate = new \DateTime($this->pandaHelper->gmtDate());
        $currentDate->sub(new \DateInterval('PT5M'));
        $minutes = $currentDate->format('Y-m-d H:i:s');

        $item = $this->getCollection()
                     ->addFieldToFilter('customer_id', $customer->getId())
                     ->addFieldToFilter('used', 0)
                     ->addFieldToFilter('is_active', 1)
                     ->addFieldToFilter('code', $code)
                     ->addFieldToFilter('sent_at', ['gt' => $minutes])
                     ->getFirstItem();

        /** @var TwoFactor $item */
        if ($item->getId()) {

            if ($remember) {
                $days = $this->scopeConfig->getValue('panda_customer/twofactor/remember_days');

                $item->setRememberHash($hash);
                $item->setRememberUntil(strtotime('now +' . $days . ' days'));
            }

            $item->setUsed(1)
                 ->setIsActive(0)
                 ->setUsedAt($this->pandaHelper->gmtDate())
                 ->save();

            return true;
        }

        $this->getResource()->getConnection()
             ->insert(
                 $this->getResource()->getTable('panda_two_factor_attempts'),
                 [
                     'customer_id'  => $customer->getId(),
                     'attempt_date' => $this->pandaHelper->gmtDate(),
                 ]
             );

        return false;
    }

    /**
     * @param $code
     *
     * @return $this
     */
    public function setCode($code)
    {

        return $this->setData('code', $code);
    }

    /**
     * @param $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {

        return $this->setData('store_id', $storeId);
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
     * @param $used
     *
     * @return $this
     */
    public function setUsed($used)
    {

        return $this->setData('used', $used);
    }

    /**
     * @param $used
     *
     * @return $this
     */
    public function setRememberHash($used)
    {

        return $this->setData('remember_hash', $used);
    }

    /**
     * @param $used
     *
     * @return $this
     */
    public function setRememberUntil($used)
    {

        return $this->setData('remember_until', $used);
    }

    /**
     * @param $sentAt
     *
     * @return $this
     */
    public function setSentAt($sentAt)
    {

        return $this->setData('sent_at', $sentAt);
    }

    /**
     * @param $message
     *
     * @return $this
     */
    public function setMessage($message)
    {

        return $this->setData('message', $message);
    }

    /**
     * @param $phone
     *
     * @return $this
     */
    public function setPhone($phone)
    {

        return $this->setData('phone', $phone);
    }

    /**
     * @param $customerEmail
     *
     * @return $this
     */
    public function setCustomerEmail($customerEmail)
    {

        return $this->setData('customer_email', $customerEmail);
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
     * @param $customerId
     *
     * @return $this
     */
    public function setCustomerId($customerId)
    {

        return $this->setData('customer_id', $customerId);
    }

    /**
     * @param $authId
     *
     * @return $this
     */
    public function setAuthId($authId)
    {

        return $this->setData('auth_id', $authId);
    }

    /**
     * @param $usedAt
     *
     * @return $this
     */
    public function setUsedAt($usedAt)
    {

        return $this->setData('used_at', $usedAt);
    }

    /**
     * @return mixed
     */
    public function getCode()
    {

        return $this->getData('code');
    }

    /**
     * @return mixed
     */
    public function getStoreId()
    {

        return $this->getData('store_id');
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {

        return $this->getData('is_active');
    }

    /**
     * @return mixed
     */
    public function getUsed()
    {

        return $this->getData('used');
    }

    /**
     * @return mixed
     */
    public function getRememberHash()
    {

        return $this->getData('remember_hash');
    }

    /**
     * @return mixed
     */
    public function getRememberUntil()
    {

        return $this->getData('remember_until');
    }

    /**
     * @return mixed
     */
    public function getSentAt()
    {

        return $this->getData('sent_at');
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {

        return $this->getData('message');
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {

        return $this->getData('phone');
    }

    /**
     * @return mixed
     */
    public function getCustomerEmail()
    {

        return $this->getData('customer_email');
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
    public function getCustomerId()
    {

        return $this->getData('customer_id');
    }

    /**
     * @return mixed
     */
    public function getAuthId()
    {

        return $this->getData('auth_id');
    }

    /**
     * @return mixed
     */
    public function getUsedAt()
    {

        return $this->getData('used_at');
    }
}
