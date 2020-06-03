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
 * @modified   03/06/20, 01:47 GMT
 *
 */

namespace Licentia\Equity\Controller\Adminhtml\TwoFactorAdmin;

/**
 * Class Auth
 *
 * @package Licentia\Equity\Controller\Adminhtml\TwoFactorAdmin
 */
class Auth extends \Licentia\Equity\Controller\Adminhtml\TwoFactorAdmin
{

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        parent::execute();

        if ($this->getRequest()->getParam('resend') == 1) {
            $resultRedirect = $this->resultRedirectFactory->create();

            $twoFactor = $this->twofactorFactory->create();

            try {
                $twoFactor->generateCode($this->userSession->getUser());

                $this->messageManager->addSuccessMessage(__('Code sent successfully'));
            } catch (\Magento\Framework\Exception\LocalizedException $exception) {
                $this->messageManager->addExceptionMessage($exception);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Error sending the code'));
            }

            return $resultRedirect->setPath('*/*/*');
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Two Factor Authentication - Admin'));
        $resultPage->addBreadcrumb(__('Sales Automation'), __('Sales Automation'));
        $resultPage->addBreadcrumb(__('Two Factor Authentication'), __('Two Factor Authentication - Admin'));

        return $resultPage;
    }
}
