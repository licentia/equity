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
 * Class UpdateSalesExtraCosts
 *
 * @package Licentia\Panda\Cron
 */
class UpdateSalesExtraCosts
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected \Licentia\Panda\Helper\Data $pandaHelper;

    /**
     * @var \Licentia\Equity\Model\Sales\ExtraCostsFactory
     */
    protected \Licentia\Equity\Model\Sales\ExtraCostsFactory $extraCostsFactory;

    /**
     * UpdateSalesExtraCosts constructor.
     *
     * @param \Licentia\Equity\Model\Sales\ExtraCostsFactory $extraCostsFactory
     * @param \Licentia\Panda\Helper\Data                    $pandaHelper
     */
    public function __construct(
        \Licentia\Equity\Model\Sales\ExtraCostsFactory $extraCostsFactory,
        \Licentia\Panda\Helper\Data $pandaHelper
    ) {

        $this->extraCostsFactory = $extraCostsFactory;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     *
     */
    public function execute()
    {

        try {
            $this->extraCostsFactory->create()->updateOrdersOtherCosts();
        } catch (\Exception $e) {
            $this->pandaHelper->logWarning($e);
        }
    }
}
