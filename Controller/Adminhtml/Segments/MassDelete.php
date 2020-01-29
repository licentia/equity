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
class MassDelete extends \Licentia\Equity\Controller\Adminhtml\Segments
{

    /**
     * @var \Licentia\Equity\Model\Segments\ListSegmentsFactory
     */
    protected $listSegmentsFactory;

    /**
     * @param Action\Context                                      $context
     * @param \Magento\Framework\View\Result\PageFactory          $resultPageFactory
     * @param \Magento\Framework\Registry                         $registry
     * @param \Licentia\Equity\Model\Segments\ListSegmentsFactory $listSegmentsFactory
     * @param \Licentia\Equity\Model\SegmentsFactory              $segmentsFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory   $resultForwardFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory    $fileFactory
     * @param \Magento\Framework\View\Result\LayoutFactory        $resultLayoutFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Equity\Model\Segments\ListSegmentsFactory $listSegmentsFactory,
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

        $this->listSegmentsFactory = $listSegmentsFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {

        parent::execute();
        $resultRedirect = $this->resultRedirectFactory->create();
        $ids = $this->getRequest()->getParam('ids');

        if (!is_array($ids)) {
            $this->messageManager->addErrorMessage(__('Please select one or more Records.'));
        } else {
            try {
                foreach ($ids as $id) {
                    $this->listSegmentsFactory->create()
                                              ->load($id)
                                              ->delete();
                }
                $this->messageManager->addSuccessMessage(
                    __(
                        'Total of %1 record(s) were removed.',
                        count($ids)
                    )
                );
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
        }

        return $resultRedirect->setPath(
            'pandae/segments/edit',
            [
                'id'     => $this->registry->registry('panda_segment')
                                           ->getId(),
                'tab_id' => 'records_section',
            ]
        );
    }
}
