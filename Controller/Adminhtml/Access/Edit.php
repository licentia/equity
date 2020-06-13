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
