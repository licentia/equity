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

namespace Licentia\Equity\Block\Adminhtml\Widget\Grid\Column\Renderer;

/**
 * Adminhtml Campaigns grid
 */
class Concat extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    /**
     * Renders grid column
     *
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {

        $dataArr = [];
        foreach ($this->getColumn()->getIndex() as $index) {
            if ($row->getData($index) !== false) {
                $dataArr[] = $row->getData($index);
            }
        }
        $data = join($this->getColumn()->getSeparator(), $dataArr);

        return $data;
    }
}
