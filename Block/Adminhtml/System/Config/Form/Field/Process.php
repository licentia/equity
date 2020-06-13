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

namespace Licentia\Equity\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Process
 *
 * @package Licentia\Panda\Block\Adminhtml\System\Config\Form\Field
 */
class Process extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element
    ) {

        $mediaDir = $this->_filesystem->getDirectoryWrite(DirectoryList::TMP);
        $filename = $mediaDir->getAbsolutePath() . '/panda.txt';

        if (is_file($filename)) {
            $text = 'Currently building metadata. Processing Subscribers with ID greater than ' .
                    (int) file_get_contents($filename);
        } else {
            $text = 'No metadata is being processed.';
        }
        $message = __($text);

        return '<br><br><strong>' . $message . '</strong>';
    }
}
