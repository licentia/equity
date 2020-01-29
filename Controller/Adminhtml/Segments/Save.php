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

use Licentia\Equity\Helper\Data;
use Magento\Backend\App\Action;

/**
 * Class Clear
 *
 * @package Licentia\Panda\Controller\Adminhtml\Segments
 */
class Save extends \Licentia\Equity\Controller\Adminhtml\Segments
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Cron\Model\ScheduleFactory
     */
    protected $scheduleFactory;

    /**
     * @param \Magento\Cron\Model\ScheduleFactory                $scheduleFactory
     * @param Action\Context                                     $context
     * @param Data                                               $helper
     * @param \Magento\Framework\View\Result\PageFactory         $resultPageFactory
     * @param \Magento\Framework\Registry                        $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Licentia\Equity\Model\SegmentsFactory             $segmentsFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory  $resultForwardFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory   $fileFactory
     * @param \Magento\Framework\View\Result\LayoutFactory       $resultLayoutFactory
     */
    public function __construct(
        \Magento\Cron\Model\ScheduleFactory $scheduleFactory,
        Action\Context $context,
        \Licentia\Equity\Helper\Data $helper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
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

        $this->scheduleFactory = $scheduleFactory;
        $this->pandaHelper = $helper;
        $this->scopeConfig = $scopeConfigInterface;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {

        parent::execute();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if data sent
        $data = $this->getRequest()->getPostValue();

        /** @var \Licentia\Equity\Model\Segments $model */
        $model = $this->registry->registry('panda_segment');
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            $model->load($id);
            if ($id != $model->getId()) {
                $this->messageManager->addErrorMessage(__('Wrong Segment specified.'));

                return $resultRedirect->setPath(
                    '*/*/edit',
                    [
                        'id'     => $model->getId(),
                        'tab_id' => $this->getRequest()->getParam('active_tab'),
                    ]
                );
            }
        }

        if ($data) {
            try {
                $validateResult = $model->validateData(new \Magento\Framework\DataObject($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->messageManager->addErrorMessage($errorMessage);
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

                if (isset($data['rule'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                    unset($data['rule']);
                }
                if (isset($data['websites_ids'])) {
                    $data['websites_ids'] = implode(',', $data['websites_ids']);
                }

                $model->loadPost($data);
                $this->_getSession()->setFormData($model->getData());

                $model->setData('controller_panda', true);
                $model->save();
                $this->_getSession()->setFormData(false);

                if ($model->getCron() == 'r') {
                    $cron = $this->scheduleFactory->create()->load('panda_build_segments_user', 'job_code');

                    if (!$cron->getId() || !$cron->getStatus() == 'pending') {
                        $this->pandaHelper->scheduleEvent('panda_build_segments_user');
                        $userId = $this->_auth->getUser()->getUserId();
                        $model->setBuild(1)
                              ->setNotifyUser($userId)
                              ->save();

                        $this->messageManager->addSuccessMessage(
                            __(
                                'This segment will be updated next time your cron runs. After that updates will occur in real time'
                            )
                        );
                    }
                }

                $this->messageManager->addSuccessMessage(__('The segment has been saved.'));
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
                    __('Something went wrong while saving the Segment. Check the error log for more information')
                );
            }
            if ($model->getId()) {
                return $resultRedirect->setPath(
                    '*/*/edit',
                    [
                        'id'     => $model->getId(),
                        'tab_id' => $this->getRequest()->getParam('active_tab'),
                    ]
                );
            } else {
                return $resultRedirect->setPath('*/*/');
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
