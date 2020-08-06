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

namespace Licentia\Equity\Controller\Adminhtml\Access;

/**
 * Class Save
 *
 * @package Licentia\Panda\Controller\Adminhtml\Access
 */
class Save extends \Licentia\Equity\Controller\Adminhtml\Access
{

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        parent::execute();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('id');
            $model = $this->registry->registry('panda_access');

            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Segment Access no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            $inputFilter = new \Zend_Filter_Input(
                [
                    'from_date' => $this->dateFilter,
                    'to_date'   => $this->dateFilter,
                ],
                [],
                $data
            );
            $data = $inputFilter->getUnescaped();

            if (!isset($data['segments_ids'])) {
                $data['segments_ids'] = [0];
            }
            if (array_search(0, $data['segments_ids']) !== false) {
                $data['segments_ids'] = [];
            }
            $data['segments_ids'] = implode(',', $data['segments_ids']);

            try {
                $model->addData($data);
                $model->setId($id);

                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Segment Access.'));
                $this->_getSession()->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'id'     => $model->getId(),
                            'tab_id' => $this->getRequest()->getParam('active_tab'),
                        ]
                    );
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the Segment Access. Check the error log for more information.')
                );
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(),]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
