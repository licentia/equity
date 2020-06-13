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

namespace Licentia\Equity\Model\Source;

/**
 * Class SegmentsRequired
 *
 * @package Licentia\Equity\Model\Source
 */
class SegmentsSelect extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * @var \Licentia\Equity\Model\SegmentsFactory
     */
    protected $segmentsFactory;

    /**
     * Segments constructor.
     *
     * @param \Licentia\Equity\Model\SegmentsFactory $segmentsFactory
     */
    public function __construct(\Licentia\Equity\Model\SegmentsFactory $segmentsFactory)
    {

        $this->segmentsFactory = $segmentsFactory;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {

        if (!$this->_options) {
            $this->_options = $this->segmentsFactory->create()->getOptionArray('Please Select');
        }

        return $this->_options;
    }
}
