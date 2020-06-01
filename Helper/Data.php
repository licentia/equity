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
 * @modified   01/06/20, 17:04 GMT
 *
 */

namespace Licentia\Equity\Helper;

/**
 * Class Data
 *
 * @package Licentia\Equity\Helper
 */
class Data extends \Licentia\Panda\Helper\Data
{

    /**
     * @return mixed
     */
    public function getBrowser()
    {

        return $this->_getRequest()->getServer('HTTP_USER_AGENT');
    }

    /**
     * @return string
     */
    public function getTwoAuthRememberCode()
    {

        return $this->encryptor->encrypt($this->customerSession->getId() . $this->_getRequest()
                                                                                ->getServer('HTTP_USER_AGENT'));
    }

    /**
     * @param \Licentia\Equity\Model\Segments $model
     */
    public function renameCustomerListingFile(\Licentia\Equity\Model\Segments $model)
    {

        $filename = realpath(dirname(__FILE__) . '/../view/adminhtml/ui_component/customer_listing.xml');
        $filenameOff = realpath(dirname(__FILE__) . '/../view/adminhtml/ui_component/customer_listing_b.xml');

        $totalSegments = $model->getCollection()->count();
        try {
            if ($totalSegments > 0 && !$filename && $filenameOff) {
                rename($filenameOff, str_replace('customer_listing_b.xml', 'customer_listing.xml', $filenameOff));

                if ($this->_cacheState->isEnabled('config')) {
                    $this->cacheTypeList->invalidate('config');
                }
            }

            if ($totalSegments == 0 && $filename && !$filenameOff) {
                rename($filename, str_replace('customer_listing.xml', 'customer_listing_b.xml', $filename));

                if ($this->_cacheState->isEnabled('config')) {
                    $this->cacheTypeList->invalidate('config');
                }
            }
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
        }

    }
}