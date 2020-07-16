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

namespace Licentia\Equity\Plugin;

class Import
{

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $segmentProducts;

    /**
     * Import constructor.
     *
     * @param \Licentia\Equity\Model\Import\SegmentProducts $segmentProducts
     */
    public function __construct(
        \Licentia\Equity\Model\Import\SegmentProducts $segmentProducts
    ) {

        $this->segmentProducts = $segmentProducts;
    }

    /**
     * After import handler
     *
     * @param \Magento\ImportExport\Model\Import $subject
     * @param boolean                            $import
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterImportSource(\Magento\ImportExport\Model\Import $subject, $import)
    {

        $this->segmentProducts->updateTotals();

        return $import;
    }
}
