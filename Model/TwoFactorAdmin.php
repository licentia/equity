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

namespace Licentia\Equity\Model;

use Magento\Store\Model\ScopeInterface;
use Laminas\Mail\Message;
use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Mime;
use Laminas\Mime\Part as MimePart;

/**
 * Class TwoFactorAdmin
 *
 * @package Licentia\Equity\Model
 */
class TwoFactorAdmin extends \Magento\Framework\Model\AbstractModel
{

    /**
     *
     */
    const XML_PATH_PANDA_FORMS_TEMPLATE = 'panda_customer/twofactor_admin/template';

    /**
     *
     */
    const ATTRIBUTE_PANDA_TWOFACTOR_ENABLED = 'panda_twofactor_enabled';

    /**
     *
     */
    const REMINDER_COOKIE_NAME = 'panda_auth';

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
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

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
     * @param \Magento\Framework\Mail\Template\TransportBuilder            $transportBuilder
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
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\UrlInterface $url,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
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
        $this->storeManager = $storeManager;
        $this->userSession = $userSession;
        $this->pandaHelper = $helperData;
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->transportBuilder = $transportBuilder;
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

        $data = $this->scopeConfig->getValue(
            'panda_customer/twofactor_admin',
            ScopeInterface::SCOPE_STORE
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

            $this->pandaHelper->logException($e);

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
            ScopeInterface::SCOPE_STORE
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

        if (!$user->getData('panda_twofactor_number') && $this->getTwoFactorType() == 'sms') {
            return false;
        }
        if (!$this->canGenerateCode($user)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Too many requests'));
        }
        $data = $this->scopeConfig->getValue(
            'panda_customer/twofactor_admin',
            ScopeInterface::SCOPE_STORE
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
        $phone = '';
        if ($this->getTwoFactorType() == 'sms') {
            $phone = $user->getData('panda_twofactor_number');

            $send = $this->pandaHelper->getSmsTransport($data['sender'])->sendSMS($phone, $message);

        } else {

            $sender = $this->pandaHelper->getSender($data['sender_email']);
            $transport = $this->pandaHelper->getSmtpTransport($sender);

            $template = $this->scopeConfig->getValue(
                self::XML_PATH_PANDA_FORMS_TEMPLATE,
                'store',
                $this->storeManager->getStore()
                                   ->getId()
            );

            $email = $user->getEmail();
            $storeName = $this->storeManager->getWebsite()->getName() . ' / ' .
                         $this->storeManager->getStore()->getName();
            $storeUrl = $this->storeManager->getStore()->getBaseUrl();
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($template)
                ->setTemplateOptions(
                    [
                        'area'  => 'adminhtml',
                        'store' => $this->storeManager->getStore()
                                                      ->getId(),
                    ]
                )
                ->setTemplateVars([
                    'code'      => $code,
                    'username'  => $user->getName(),
                    'storeName' => $storeName,
                    'storeUrl'  => $storeUrl,
                ])
                ->setFrom('support')
                ->addTo($email)
                ->getTransport();

            $result = $transport->sendMessage();

            $send = true;
        }

        if ($send) {
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
        } else {
            throw  new \Magento\Framework\Exception\LocalizedException(__('Error Sending SMS'));
        }

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
     * @return mixed
     */
    public function getTwoFactorType()
    {

        return $this->scopeConfig->getValue(
            'panda_customer/twofactor/type',
            ScopeInterface::SCOPE_STORE
        );
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
