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

namespace Licentia\Equity\Controller\Adminhtml\CustomerPrices;

/**
 * Class Delete
 *
 * @package Licentia\Panda\Controller\Adminhtml\Access
 */
class Deleteprices extends \Licentia\Equity\Controller\Adminhtml\CustomerPrices
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        parent::execute();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $ids = $this->getRequest()->getParam('ids');

        if (!is_array($ids)) {
            $this->messageManager->addErrorMessage(__('Please select one or more Records.'));
        } else {
            try {
                foreach ($ids as $record) {
                    $this->customerPricesFactory->create()
                                                ->load($record)
                                                ->delete();
                }
                $this->messageManager->addSuccessMessage(
                    __('Total of %1 record(s) were deleted.', count($ids))
                );
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while deleting the events.')
                );
            }
        }

        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
