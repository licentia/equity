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

namespace Licentia\Equity\Controller\TwoFactor;

/**
 * Class Save
 *
 * @package Licentia\Panda\Controller\TwoFactor
 */
class Save extends \Licentia\Equity\Controller\TwoFactor
{

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {

        parent::execute();

        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->_redirect('pandae/twofactor/auth');
        }

        $code = $this->getRequest()->getParam('sms_code');

        $customer = $this->customerSession->getCustomer();
        $customerId = $customer->getId();

        if (!$customerId) {
            $this->customerSession->setData('panda_twofactor_required', false);

            return $this->_redirect('/');
        }

        $smsNumber = $this->getRequest()->getParam('cellphone');

        if ($smsNumber) {
            if (!$smsNumber = $this->pandaHelper->isPhoneNumberValid($smsNumber)) {
                $this->messageManager->addErrorMessage(
                    __(
                        'Invalid Cellphone. Please use the following format: CountryCode-CellphoneNumber. Ex: 351-989647542'
                    )
                );
            } else {
                $customer->setData('panda_twofactor_number', $smsNumber)
                         ->setAttributeSetId(\Magento\Customer\Api\CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER)
                         ->getResource()
                         ->save($customer);

                if ($this->customerSession->getCustomer()->getData('panda_twofactor_number')) {
                    $this->messageManager->addSuccessMessage(__('Cellphone Successfully Updated'));

                    try {
                        $this->twofactorFactory->create()->generateCode($this->customerSession->getCustomer());
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

            return $this->_redirect('pandae/twofactor/auth');
        }

        if ($customerId === null) {
            $this->customerSession->setData('panda_twofactor_required', false);
        } else {
            try {
                $auth = $this->twofactorFactory->create()->validateCode($customer, $code);

                if ($auth) {
                    $this->customerSession->setData('panda_twofactor_required', false);

                    $url = $this->customerSession->getData('panda_twofactor_referer');

                    return $this->resultRedirectFactory->create()->setUrl($url);
                } else {
                    $this->messageManager->addErrorMessage(__('Invalid SMS Code. Please try again'));

                    return $this->_redirect('pandae/twofactor/auth');
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while validating the code.'));
            }
        }

        $this->_redirect('pandae/twofactor/auth');
    }
}
