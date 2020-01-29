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
