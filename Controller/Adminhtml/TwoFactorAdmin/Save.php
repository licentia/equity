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
 * @modified   03/06/20, 02:12 GMT
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
