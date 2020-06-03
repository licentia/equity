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
 * @modified   03/06/20, 01:17 GMT
 *
 */

namespace Licentia\Equity\Model;

/**
 * Class TwoFactorAdmin
 *
 * @package Licentia\Equity\Model
 */
class TwoFactorAdmin extends \Magento\Framework\Model\AbstractModel
{

    const REMINDER_COOKIE_NAME = 'panda_auth_admin';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_two_factor_admin';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'panda_two_factor_admin';

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $userSession;

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected $pandaHelper;

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

        $this->_init(ResourceModel\TwoFactorAdmin::class);
    }

    /**
     * TwoFactorAdmin constructor.
     *
     * @param \Magento\Framework\UrlInterface                              $url
     * @param \Magento\Backend\Model\Auth\Session                          $userSession
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
        \Magento\Backend\Model\Auth\Session $userSession,
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
        $this->userSession = $userSession;
        $this->pandaHelper = $helperData;
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     *
     */
    public function logoutUser()
    {

        if (!$this->scopeConfig->isSetFlag('panda_customer/twofactor_admin/enabled')) {
            return;
        }
        /** @var \Magento\User\Model\User $user */
        $user = $this->userSession->getUser();

        if ($user) {
            $item = $this->getCollection()
                         ->addFieldToFilter('remember_hash', $this->pandaHelper->getTwoAuthRememberCode())
                         ->addFieldToFilter('user_id', $user->getId());

            /** @var TwoFactorAdmin $log */
            foreach ($item as $log) {
                $log->setRememberHash('')
                    ->save();
            }
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
                     ->addFieldToFilter('user_id', $this->userSession->getUser()->getId())
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

        if (!$this->scopeConfig->isSetFlag('panda_customer/twofactor_admin/enabled')) {
            return true;
        }

        /** @var \Magento\User\Model\User $user */
        $user = $event->getUser();

        $data = $this->scopeConfig->getValue(
            'panda_customer/twofactor_admin',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (isset($data['allow_remember']) && $data['allow_remember'] == 1) {
            if ($this->validateRemember()) {
                return true;
            }
        }

        $this->userSession->setData('panda_twofactor_required', true);

        try {
            $this->generateCode($this->userSession->getUser());
        } catch (\Exception $e) {

            $this->_logger->critical($e->getMessage());

            return false;
        }

        return false;
    }

    /**
     * @param \Magento\User\Model\User $user
     *
     * @return bool|int
     */
    public function canGenerateCode(\Magento\User\Model\User $user)
    {

        $data = $this->scopeConfig->getValue(
            'panda_customer/twofactor_admin',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $resource = $this->getResource();
        $connection = $resource->getConnection();

        $currentDate = new \DateTime($this->pandaHelper->gmtDate());
        $currentDate->sub(new \DateInterval('PT1H'));
        $hourDate = $currentDate->format('Y-m-d H:i:s');

        $attemptsHour = $connection->fetchOne(
            $connection->select()
                       ->from($resource->getTable('panda_two_factor_attempts_admin'), ['COUNT(*)'])
                       ->where('user_id=?', $user->getId())
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
                       ->from($resource->getTable('panda_two_factor_attempts_admin'), ['COUNT(*)'])
                       ->where('user_id=?', $user->getId())
                       ->where("attempt_date>=?", $dayDate)
        );

        if ($attemptsDay >= $data['max_resends_day']) {
            return -1;
        }

        return true;
    }

    /**
     * @param \Magento\User\Model\User $user
     *
     * @return bool|TwoFactorAdmin
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generateCode(\Magento\User\Model\User $user)
    {

        if (!$user->getData('panda_twofactor_number')) {
            return false;
        }
        if (!$this->canGenerateCode($user)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Too many requests'));
        }
        $data = $this->scopeConfig->getValue(
            'panda_customer/twofactor_admin',
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

        $phone = $user->getData('panda_twofactor_number');

        $this->pandaHelper->getSmsTransport($data['sender'])->sendSMS($phone, $message);

        $new = [];
        $new['user_id'] = $user->getId();
        $new['user_name'] = $user->getName();
        $new['user_email'] = $user->getEmail();
        $new['phone'] = $phone;
        $new['code'] = $code;
        $new['message'] = $message;
        $new['used'] = 0;
        $new['sent_at'] = $this->pandaHelper->gmtDate();
        $new['is_active'] = 1;

        $collection = $this->getCollection()
                           ->addFieldToFilter('user_id', $user->getId())
                           ->addFieldToFilter('is_active', 1);

        /** @var TwoFactor $item */
        foreach ($collection as $item) {
            $item->setIsActive(0)->setUsed(0)->save();
        }

        return $this->setData([])->setData($new)->save();
    }

    /**
     * @param \Magento\User\Model\User         $user
     * @param                                  $code
     * @param                                  $remember
     * @param                                  $hash
     *
     * @return bool
     */
    public function validateCode(
        \Magento\User\Model\User $user,
        $code,
        $remember,
        $hash
    ) {

        $currentDate = new \DateTime($this->pandaHelper->gmtDate());
        $currentDate->sub(new \DateInterval('PT5M'));
        $minutes = $currentDate->format('Y-m-d H:i:s');

        $item = $this->getCollection()
                     ->addFieldToFilter('user_id', $user->getId())
                     ->addFieldToFilter('used', 0)
                     ->addFieldToFilter('is_active', 1)
                     ->addFieldToFilter('code', $code)
                     ->addFieldToFilter('sent_at', ['gt' => $minutes])
                     ->getFirstItem();

        /** @var TwoFactor $item */
        if ($item->getId()) {

            if ($remember) {
                $days = $this->scopeConfig->getValue('panda_customer/twofactor_admin/remember_days');

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
                 $this->getResource()->getTable('panda_two_factor_attempts_admin'),
                 [
                     'user_id'      => $user->getId(),
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
     * @param $userEmail
     *
     * @return $this
     */
    public function setUserEmail($userEmail)
    {

        return $this->setData('user_email', $userEmail);
    }

    /**
     * @param $userName
     *
     * @return $this
     */
    public function setUserName($userName)
    {

        return $this->setData('user_name', $userName);
    }

    /**
     * @param $userId
     *
     * @return $this
     */
    public function setUserId($userId)
    {

        return $this->setData('user_id', $userId);
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
    public function getUserEmail()
    {

        return $this->getData('user_email');
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {

        return $this->getData('user_name');
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {

        return $this->getData('user_id');
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
