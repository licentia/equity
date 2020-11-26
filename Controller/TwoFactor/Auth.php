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

namespace Licentia\Equity\Controller\TwoFactor;

/**
 * Class Auth
 *
 * @package Licentia\Panda\Controller\Subscriber
 */
class Auth extends \Licentia\Equity\Controller\TwoFactor
{

    /**
     * @return $this|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {

        parent::execute();
        if ($this->getRequest()->getParam('resend') == 1) {
            $resultRedirect = $this->resultRedirectFactory->create();

            $twoFactor = $this->twofactorFactory->create();

            try {
                $twoFactor->generateCode($this->customerSession->getCustomer());

                $this->messageManager->addSuccessMessage(__('Code sent successfully'));
            } catch (\Magento\Framework\Exception\LocalizedException $exception) {
                $this->messageManager->addExceptionMessage($exception);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Error sending the code'.$e->getMessage()));
            }

            return $resultRedirect->setPath('*/*/*');
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->initLayout();

        if (!$this->customerSession->getData('panda_twofactor_referer') &&
            stripos('pandae/twofactor/', $this->_redirect->getRefererUrl()) === false) {
            $this->customerSession->setData('panda_twofactor_referer', $this->_redirect->getRefererUrl());
        }

        return $resultPage;
    }
}
