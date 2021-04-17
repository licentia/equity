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

namespace Licentia\Equity\Cron;

/**
 * Class UpdateSegmentsEvolutions
 *
 * @package Licentia\Panda\Cron
 */
class UpdateSegmentsEvolutions
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected \Licentia\Panda\Helper\Data $pandaHelper;

    /**
     * @var  \Licentia\Equity\Model\ResourceModel\Evolutions
     */
    protected \Licentia\Equity\Model\ResourceModel\Evolutions $evolutionsResource;

    /**
     * UpdateSegmentsEvolutions constructor.
     *
     * @param \Licentia\Panda\Helper\Data                     $pandaHelper
     * @param \Licentia\Equity\Model\ResourceModel\Evolutions $evolutionsResource
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Equity\Model\ResourceModel\Evolutions $evolutionsResource
    ) {

        $this->evolutionsResource = $evolutionsResource;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     *
     */
    public function execute()
    {

        try {
            $this->evolutionsResource->updateEvolutions();
        } catch (\Exception $e) {
            $this->pandaHelper->logWarning($e);
        }
    }
}
