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

use Magento\Backend\App\Action;

/**
 * Class Clear
 *
 * @package Licentia\Panda\Controller\Adminhtml\Segments
 */
class Deleteevo extends \Licentia\Equity\Controller\Adminhtml\Segments
{

    /**
     * @var \Licentia\Equity\Model\EvolutionsFactory
     */
    protected $evolutionsFactory;

    /**
     * Deleteevo constructor.
     *
     * @param Action\Context                                    $context
     * @param \Magento\Framework\View\Result\PageFactory        $resultPageFactory
     * @param \Magento\Framework\Registry                       $registry
     * @param \Licentia\Equity\Model\SegmentsFactory            $segmentsFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\LayoutFactory      $resultLayoutFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory  $fileFactory
     * @param \Licentia\Equity\Model\EvolutionsFactory          $evolutionsFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Equity\Model\SegmentsFactory $segmentsFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Licentia\Equity\Model\EvolutionsFactory $evolutionsFactory
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

        $this->evolutionsFactory = $evolutionsFactory;
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
                foreach ($ids as $record) {
                    $this->evolutionsFactory->create()
                                            ->load($record)
                                            ->delete();
                }
                $this->messageManager->addSuccessMessage(
                    __(
                        'Total of %1 record(s) were deleted.',
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
                    __('Something went wrong while deleting the events.')
                );
            }
        }

        return $resultRedirect->setPath(
            'pandae/segments/edit',
            [
                'id'     => $this->registry->registry('panda_segment')
                                           ->getId(),
                'tab_id' => 'evolution_section',
            ]
        );
    }
}
