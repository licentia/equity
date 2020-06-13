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

namespace Licentia\Equity\Controller\Adminhtml\ExtraCosts;

/**
 * Class Save
 *
 * @package Licentia\Panda\Controller\Adminhtml\ExtraCosts
 */
class Save extends \Licentia\Equity\Controller\Adminhtml\ExtraCosts
{

    /**
     */
    public function execute()
    {

        parent::execute();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if data sent
        $data = $this->getRequest()->getParams();
        if ($data) {
            $id = $this->getRequest()->getParam('id');

            $model = $this->registry->registry('panda_extra_cost');

            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Extra Cost no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            if (isset($data['shipping_methods']) && is_array($data['shipping_methods'])) {
                $data['shipping_methods'] = implode(',', $data['shipping_methods']);
            }
            if (isset($data['payment_methods']) && is_array($data['payment_methods'])) {
                $data['payment_methods'] = implode(',', $data['payment_methods']);
            }

            try {
                $model->setData($data);
                if ($id) {
                    $model->setId($id);
                }

                $model->save();

                $this->_getSession()->setFormData(false);
                $this->messageManager->addSuccessMessage(__('You saved the Extra Cost.'));

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
                    __('Something went wrong while saving the Extra Cost. Check the error log for more information.')
                );
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath(
                '*/*/edit',
                [
                    'id'     => $model->getId(),
                    'tab_id' => $this->getRequest()->getParam('active_tab'),
                ]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }

}
