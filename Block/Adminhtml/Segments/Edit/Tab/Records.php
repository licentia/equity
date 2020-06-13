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

namespace Licentia\Equity\Block\Adminhtml\Segments\Edit\Tab;

/**
 * Class Records
 *
 * @package Licentia\Panda\Block\Adminhtml\Segments\Edit\Tab
 */
class Records extends \Magento\Framework\View\Element\Text\ListText implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {

        return __('Records');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {

        return __('Records');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {

        return $this->hasData('can_show_tab') ? $this->getData('can_show_tab') : true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {

        return false;
    }
}
