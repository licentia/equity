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

namespace Licentia\Equity\Controller\Adminhtml\Access;

/**
 * Class Edit
 *
 * @package Licentia\Panda\Controller\Adminhtml\Access
 */
class Edit extends \Licentia\Equity\Controller\Adminhtml\Access
{

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {

        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Licentia_Equity::access')
                   ->addBreadcrumb(__('Message Access'), __('Message Access'))
                   ->addBreadcrumb(__('Manage Message Access'), __('Manage Message Access'));

        return $resultPage;
    }

    /**
     * @return $this|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        parent::execute();

        $id = $this->getRequest()->getParam('id');

        /** @var \Licentia\Equity\Model\Access $model */
        $model = $this->registry->registry('panda_access');

        $entityType = $this->getRequest()->getParam('entity_type');
        if ($entityType) {
            if (!array_key_exists($entityType, \Licentia\Equity\Model\Access::getAccessTypes())) {
                $this->messageManager->addErrorMessage(__('Invalid Entity Type.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        if ($id) {
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Segment Access no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        if (!$model->getSegmentsIds()) {
            $model->setSegmentsIds('0');
        }
        $model->setSegmentsIds(explode(',', $model->getSegmentsIds()));

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Message Access') : __('New Segment Access'),
            $id ? __('Edit Message Access') : __('New Segment Access')
        );
        $resultPage->getConfig()
                   ->getTitle()->prepend(__('Segment Access'));
        $resultPage->getConfig()
                   ->getTitle()->prepend($model->getId() ? $model->getName() : __('New Segment Access'));

        $resultPage->addContent(
            $resultPage->getLayout()
                       ->createBlock('Licentia\Equity\Block\Adminhtml\Access\Edit')
        )
                   ->addLeft(
                       $resultPage->getLayout()
                                  ->createBlock('Licentia\Equity\Block\Adminhtml\Access\Edit\Tabs')
                   );

        return $resultPage;
    }
}
