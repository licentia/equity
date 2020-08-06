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
        $resultPage->getConfig()->getTitle()->prepend(__('Two-Factor Authentication - Admin'));
        $resultPage->addBreadcrumb(__('Sales Automation'), __('Sales Automation'));
        $resultPage->addBreadcrumb(__('Two-Factor Authentication'), __('Two-Factor Authentication - Admin'));

        return $resultPage;
    }
}
