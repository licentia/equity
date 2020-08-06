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
 * Class Clear
 *
 * @package Licentia\Panda\Controller\Adminhtml\Segments
 */
class Edit extends \Licentia\Equity\Controller\Adminhtml\Segments
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
        $resultPage->setActiveMenu('Licentia_Equity::segments')
                   ->addBreadcrumb(__('Customer Segments'), __('Customer Segments'))
                   ->addBreadcrumb(__('Manage Segment'), __('Manage Segment'));

        return $resultPage;
    }

    /**
     * @return $this|\Magento\Backend\Model\View\Result\Page
     * @throws \Exception
     */
    public function execute()
    {

        parent::execute();
        $id = $this->getRequest()->getParam('id');
        $segment = $this->registry->registry('panda_segment');

        if ($id) {
            if (!$segment->getId()) {
                $this->messageManager->addErrorMessage(__('This Segment no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        if ($this->getRequest()->getParam('refresh') == 'now') {
            $segment->updateSegmentRecords();
            $this->messageManager->addSuccessMessage(__('Segment records updated'));
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $segment->setData($data);
        }

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Segment') : __('New Segment'),
            $id ? __('Edit Segment') : __('New Segment')
        );
        $resultPage->getConfig()
                   ->getTitle()->prepend(__('Customer Segments'));
        $resultPage->getConfig()
                   ->getTitle()->prepend($segment->getId() ? $segment->getName() : __('New Segment'));

        $resultPage->addContent(
            $resultPage->getLayout()
                       ->createBlock('Licentia\Equity\Block\Adminhtml\Segments\Edit')
        )
                   ->addLeft(
                       $resultPage->getLayout()
                                  ->createBlock('Licentia\Equity\Block\Adminhtml\Segments\Edit\Tabs')
                   );

        return $resultPage;
    }
}
