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

namespace Licentia\Equity\Controller\Adminhtml\Formulas;

/**
 * Class Edit
 *
 * @package Licentia\Panda\Controller\Adminhtml\Formulas
 */
class Edit extends \Licentia\Equity\Controller\Adminhtml\Formulas
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
        $resultPage->setActiveMenu('Licentia_Equity::formulas')
                   ->addBreadcrumb(__('Types'), __('Formulas'))
                   ->addBreadcrumb(__('Manage Formulas'), __('Manage Formulas'));

        return $resultPage;
    }

    /**
     * @return $this|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        parent::execute();
        $id = $this->getRequest()->getParam('id');
        $model = $this->registry->registry('panda_formula');

        if ($id && !$model->getId()) {
            $this->messageManager->addErrorMessage(__('This Formula no longer exists.'));
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Formula') : __('New Formula'),
            $id ? __('Edit Formula') : __('New Formula')
        );
        $resultPage->getConfig()
                   ->getTitle()->prepend(__('Formulas'));
        $resultPage->getConfig()
                   ->getTitle()->prepend($model->getId() ? __('Edit Formulas') : __('New Formula'));

        $resultPage->addContent(
            $resultPage->getLayout()
                       ->createBlock('Licentia\Equity\Block\Adminhtml\Formulas\Edit')
        )
                   ->addLeft(
                       $resultPage->getLayout()
                                  ->createBlock('Licentia\Equity\Block\Adminhtml\Formulas\Edit\Tabs')
                   );

        return $resultPage;
    }
}
