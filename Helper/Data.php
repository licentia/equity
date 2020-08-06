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

namespace Licentia\Equity\Helper;

/**
 * Class Data
 *
 * @package Licentia\Equity\Helper
 */
class Data extends \Licentia\Panda\Helper\Data
{

    /**
     * @return string
     */
    public function getTwoAuthRememberCode()
    {

        return sha1($this->customerSession->getId() .
                    $this->_getRequest()->getServer('HTTP_USER_AGENT') .
                    $this->_getRequest()->getServer('SERVER_NAME'));
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
            $this->logWarning($e);
        }

    }
}