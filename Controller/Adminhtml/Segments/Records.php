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

use Magento\Backend\App\Action;

/**
 * Class Clear
 *
 * @package Licentia\Panda\Controller\Adminhtml\Segments
 */
class Records extends \Licentia\Equity\Controller\Adminhtml\Segments
{

    /**
     * @var \Magento\Cron\Model\ScheduleFactory
     */
    protected $cronFactory;

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @param Action\Context                                    $context
     * @param \Magento\Cron\Model\ScheduleFactory               $scheduleFactory
     * @param \Magento\Framework\View\Result\PageFactory        $resultPageFactory
     * @param \Magento\Framework\Registry                       $registry
     * @param \Licentia\Equity\Helper\Data                      $pandaHelper
     * @param \Licentia\Equity\Model\SegmentsFactory            $segmentsFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory  $fileFactory
     * @param \Magento\Framework\View\Result\LayoutFactory      $resultLayoutFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Cron\Model\ScheduleFactory $scheduleFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Equity\Helper\Data $pandaHelper,
        \Licentia\Equity\Model\SegmentsFactory $segmentsFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
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

        $this->pandaHelper = $pandaHelper;
        $this->cronFactory = $scheduleFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     *
     * @throws \Exception
     */
    public function execute()
    {

        parent::execute();
        $resultRedirect = $this->resultRedirectFactory->create();

        $id = $this->getRequest()->getParam('id');

        $segment = $this->registry->registry('panda_segment');

        if (!$segment->getId()) {
            $this->messageManager->addErrorMessage(__('This segment no longer exists.'));

            return $resultRedirect->setPath('*/*/');
        }

        if ($this->getRequest()->getParam('refresh') == 'now') {
            $this->segmentsFactory->create()->updateSegmentRecords($id);
            $this->messageManager->addSuccessMessage(__('Segment records updated'));
        } elseif ($this->getRequest()->getParam('refresh')) {

            /** @var \Magento\Cron\Model\Schedule $cron */
            $cron = $this->cronFactory->create()
                                      ->getCollection()
                                      ->addFieldToFilter('job_code', 'panda_build_segments_user')
                                      ->setOrder('schedule_id', 'DESC')
                                      ->setPageSize(1)
                                      ->getFirstItem();

            if (!$cron->getId() || $cron->getStatus() != 'pending') {
                $this->pandaHelper->scheduleEvent('panda_build_segments_user');

                $userId = $this->_auth->getUser()->getUserId();

                $segment->setData('build', 1)
                        ->setData('notify_user', $userId)
                        ->save();
            }

            $this->messageManager->addSuccessMessage(__('Report will be built next time your cron runs'));
        }

        return $resultRedirect->setPath(
            '*/*/edit',
            [
                'id'     => $segment->getId(),
                'tab_id' => 'records_section',
            ]
        );
    }
}
