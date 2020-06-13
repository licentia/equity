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

namespace Licentia\Equity\Controller\Adminhtml\TwoFactorAdmin;

/**
 * Class Save
 *
 * @package Licentia\Panda\Controller\TwoFactor
 */
class Save extends \Licentia\Equity\Controller\Adminhtml\TwoFactorAdmin
{

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {

        parent::execute();

        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->_redirect('pandae/twofactoradmin/auth');
        }

        $code = $this->getRequest()->getParam('sms_code');
        $allowRemember = $this->getRequest()->getParam('sms_remember_browser');

        $user = $this->userSession->getUser();
        $userId = $user->getId();

        $smsNumber = $this->getRequest()->getParam('cellphone');

        if ($smsNumber) {
            if (!$smsNumber = $this->pandaHelper->isPhoneNumberValid($smsNumber)) {
                $this->messageManager->addErrorMessage(
                    __(
                        'Invalid Cellphone. Please use the following format: CountryCode-CellphoneNumber. Ex: 351-989647542'
                    )
                );
            } else {
                $user->setData('panda_twofactor_number', $smsNumber)->save();

                if ($this->userSession->getUser()->getData('panda_twofactor_number')) {
                    $this->messageManager->addSuccessMessage(__('Cellphone Successfully Updated'));

                    try {
                        $this->twofactorFactory->create()->generateCode($this->userSession->getUser());
                        $this->messageManager->addSuccessMessage(__('Code Sent.'));
                    } catch (\Magento\Framework\Exception\LocalizedException $exception) {
                        $this->messageManager->addExceptionMessage($exception);
                    } catch (\Exception $exception) {
                        $this->messageManager->addErrorMessage(__('Something went wrong while sending the code.'));
                    }
                } else {
                    $this->messageManager->addErrorMessage(
                        __('Something went wrong while sending updating your cellphone.')
                    );
                }
            }

            return $this->_redirect('pandae/twofactoradmin/auth');
        }

        try {

            $hash = $this->pandaHelper->getTwoAuthRememberCode();
            $auth = $this->twofactorFactory->create()->validateCode($user, $code, $allowRemember, $hash);

            if ($auth) {
                $this->userSession->setData('panda_twofactor_required', false);

                $url = $this->userSession->getData('panda_twofactor_referer');

                if ($allowRemember) {
                    $allow = $this->scopeConfig->isSetFlag('panda_customer/twofactor_admin/allow_remember');
                    $days = $this->scopeConfig->getValue('panda_customer/twofactor_admin/remember_days');

                    if ($allow) {
                        $metadata = $this->cookieMetadataFactory->setDuration(3600 * 24 * $days)
                                                                ->setPath('/');

                        $this->cookieManager->setPublicCookie(
                            \Licentia\Equity\Model\TwoFactorAdmin::REMINDER_COOKIE_NAME, $hash, $metadata);
                    }
                }

                return $this->_redirect('adminhtml/dashboard/');
            } else {
                $this->messageManager->addErrorMessage(__('Invalid SMS Code. Please try again'));

                return $this->_redirect('pandae/twofactoradmin/auth');
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while validating the code.'));
        }

        return $this->_redirect('pandae/twofactor/auth');
    }
}
