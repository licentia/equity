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

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;

/**
 * Class ExportXml
 *
 * @package Licentia\Panda\Controller\Adminhtml\Forms
 */
class ExportXml extends \Licentia\Equity\Controller\Adminhtml\Segments
{

    /**
     * Export review product detail report to CSV format
     *
     * @return ResponseInterface
     * @throws \Exception
     */
    public function execute()
    {

        parent::execute();

        $form = $this->registry->registry('panda_segment');

        $name = preg_replace('/\W/', '_', $form->getName());
        $name = strtolower($name) . '-' . date('Y-m-d H_i_s');

        $fileName = 'form_entries' . $name . '.xml';
        $content = $this->_view->getLayout()
                               ->createBlock(
                                   'Licentia\Equity\Block\Adminhtml\Segments\Records\Grid'
                               )
                               ->getXml();

        return $this->fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}
