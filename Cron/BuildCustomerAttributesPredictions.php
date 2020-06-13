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
 * Class BuildCustomerAttributesPredictions
 *
 * @package Licentia\Panda\Cron
 */
class BuildCustomerAttributesPredictions
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Licentia\Equity\Model\KpisFactory
     */
    protected $kpisFactory;

    /**
     * BuildCustomerAttributesPredictions constructor.
     *
     * @param \Licentia\Equity\Model\KpisFactory $kpisFactory
     * @param \Licentia\Panda\Helper\Data        $pandaHelper
     */
    public function __construct(
        \Licentia\Equity\Model\KpisFactory $kpisFactory,
        \Licentia\Panda\Helper\Data $pandaHelper
    ) {

        $this->kpisFactory = $kpisFactory;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     * @return $this|bool|\Licentia\Equity\Model\Kpis
     */
    public function execute()
    {

        try {
            $this->kpisFactory->create()->buildCustomerAttributesPredictions();
        } catch (\Exception $e) {
            $this->pandaHelper->logWarning($e);
        }

        return true;
    }
}
