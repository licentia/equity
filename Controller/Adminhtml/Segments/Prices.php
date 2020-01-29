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
 * Class Delete
 *
 * @package Licentia\Panda\Controller\Adminhtml\Segments
 */
class Prices extends \Licentia\Equity\Controller\Adminhtml\Segments
{

    /**
     * @var \Licentia\Equity\Model\PricesFactory
     */
    protected $pricesFactory;

    /**
     * @var \Licentia\Equity\Model\IndexFactory
     */
    protected $indexFactory;

    /**
     * Prices constructor.
     *
     * @param Action\Context                                    $context
     * @param \Licentia\Equity\Model\PricesFactory              $pricesFactory
     * @param \Licentia\Equity\Model\IndexFactory               $indexFactory
     * @param \Magento\Framework\View\Result\PageFactory        $resultPageFactory
     * @param \Magento\Framework\Registry                       $registry
     * @param \Licentia\Equity\Model\SegmentsFactory            $segmentsFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\LayoutFactory      $resultLayoutFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory  $fileFactory
     */
    public function __construct(
        Action\Context $context,
        \Licentia\Equity\Model\PricesFactory $pricesFactory,
        \Licentia\Equity\Model\IndexFactory $indexFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Equity\Model\SegmentsFactory $segmentsFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
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

        $this->pricesFactory = $pricesFactory;
        $this->indexFactory = $indexFactory;
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        parent::execute();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $collection = $this->pricesFactory->create()->getCollection();
            foreach ($collection as $item) {
                $item->delete();
            }
            $collectionIndex = $this->indexFactory->create()->getCollection();
            foreach ($collectionIndex as $item) {
                $item->delete();
            }
            $this->messageManager->addSuccessMessage(__('Segments Prices Removed Successfully'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
