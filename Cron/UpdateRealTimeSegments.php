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

namespace Licentia\Equity\Cron;

/**
 * Class UpdateRealTimeSegments
 *
 * @package Licentia\Panda\Cron
 */
class UpdateRealTimeSegments
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Licentia\Equity\Model\SegmentsFactory
     */
    protected $segmentsFactory;

    /**
     * UpdateRealTimeSegments constructor.
     *
     * @param \Licentia\Equity\Model\SegmentsFactory             $segmentsFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Licentia\Panda\Helper\Data                        $pandaHelper
     */
    public function __construct(
        \Licentia\Equity\Model\SegmentsFactory $segmentsFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Licentia\Panda\Helper\Data $pandaHelper
    ) {

        $this->segmentsFactory = $segmentsFactory;
        $this->scopeConfig = $scopeConfigInterface;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     * @return bool|void
     */
    public function execute()
    {

        try {
            return $this->segmentsFactory->create()->updateRealTimeCron();
        } catch (\Exception $e) {
            $this->pandaHelper->logException($e);
        }

        return true;
    }
}
