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
 * Class Edit
 *
 * @package Licentia\Panda\Controller\Adminhtml\ExtraCosts
 */
class Edit extends \Licentia\Equity\Controller\Adminhtml\ExtraCosts
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
        $resultPage->setActiveMenu('Licentia_Equity::extra_costs')
                   ->addBreadcrumb(__('Sales Automation'), __('Sales Automation'))
                   ->addBreadcrumb(__('Manage Extra Costs'), __('Manage Extra Costs'));

        return $resultPage;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {

        parent::execute();
        $id = $this->getRequest()->getParam('id');
        /** @var \Licentia\Equity\Model\Sales\ExtraCosts $model */
        $model = $this->registry->registry('panda_extra_cost');

        if ($id) {
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Extra Cost no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Extra Cost') : __('New Extra Cost'),
            $id ? __('Edit Extra Cost') : __('New Extra Cost')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Extra Cost'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getTitle() : __('Extra Cost'));

        $resultPage->addContent(
            $resultPage->getLayout()
                       ->createBlock('Licentia\Equity\Block\Adminhtml\ExtraCosts\Edit')
        )
                   ->addLeft(
                       $resultPage->getLayout()
                                  ->createBlock('Licentia\Equity\Block\Adminhtml\ExtraCosts\Edit\Tabs')
                   );

        return $resultPage;
    }

}
