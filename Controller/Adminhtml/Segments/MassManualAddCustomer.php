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

namespace Licentia\Equity\Controller\Adminhtml\Segments;

use Licentia\Panda\Model\ResourceModel\Subscribers\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassManualAddCustomer
 *
 * @package Licentia\Panda\Controller\Adminhtml\Segments
 */
class MassManualAddCustomer extends \Licentia\Equity\Controller\Adminhtml\Segments
{

    /**
     * @var \Licentia\Equity\Model\Segments\ListSegmentsFactory
     */
    protected $listSegmentsFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Filter
     */

    protected $filter;

    /**
     * @param Action\Context                                      $context
     * @param \Magento\Framework\View\Result\PageFactory          $resultPageFactory
     * @param \Magento\Framework\Registry                         $registry
     * @param \Licentia\Equity\Model\SegmentsFactory              $segmentsFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory   $resultForwardFactory
     * @param \Magento\Framework\View\Result\LayoutFactory        $resultLayoutFactory
     * @param \Licentia\Equity\Model\Segments\ListSegmentsFactory $listSegmentsFactory
     * @param Filter                                              $filter
     * @param CollectionFactory                                   $collectionFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory    $fileFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Equity\Model\SegmentsFactory $segmentsFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Licentia\Equity\Model\Segments\ListSegmentsFactory $listSegmentsFactory,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {

        parent::__construct(
            $context,
            $resultPageFactory,
            $registry,
            $segmentsFactory,
            $resultForwardFactory,
            $resultLayoutFactory,
            $fileFactory
        );

        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->listSegmentsFactory = $listSegmentsFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {

        $collection = $this->filter->getCollection($this->collectionFactory->create());

        $segmentId = $this->getRequest()->getParam('group');
        $changesNum = 0;
        $field = $this->getRequest()->getParam('namespace');

        if ($field == 'panda_subscriber_listing') {
            $field = 'subscriber_id';
        } else {
            $field = 'customer_id';
        }

        $selected = $this->getRequest()->getParam('selected');
        if (is_array($selected) && count($selected)) {
            $arrayCollection = $selected;
        } else {
            $arrayCollection = $collection->getData();
        }

        try {
            foreach ($arrayCollection as $id) {
                $this->listSegmentsFactory->create()->addRecordToSegment($id, $segmentId, $field);
                $changesNum++;
            }

            $this->messageManager->addSuccessMessage(__('Total of %1 record(s) were changed.', $changesNum));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Something went wrong while performing the action.')
            );
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
