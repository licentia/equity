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

namespace Licentia\Equity\Controller\Adminhtml\Segments;

/**
 * Class Delete
 *
 * @package Licentia\Panda\Controller\Adminhtml\Segments
 */
class Delete extends \Licentia\Equity\Controller\Adminhtml\Segments
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
        $model = $this->registry->registry('panda_segment');

        if ($model->getId()) {
            try {
                $model->delete();
                $this->messageManager->addSuccessMessage(__('You deleted the Segment.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while deleting the Segment.')
                );
            }

            return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
        }
        $this->messageManager->addErrorMessage(__('We can\'t find an Segment to delete.'));

        return $resultRedirect->setPath('*/*/');
    }
}
