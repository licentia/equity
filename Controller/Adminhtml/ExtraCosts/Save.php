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
