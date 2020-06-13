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

namespace Licentia\Equity\Observer\Admin;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class ReindexAll
 *
 * @package Licentia\Panda\Observer
 */
class ReindexAll implements ObserverInterface
{

    /**
     * @var \Licentia\Equity\Model\ResourceModel\IndexFactory
     */
    protected $indexFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * ReindexAll constructor.
     *
     * @param \Licentia\Panda\Helper\Data                       $pandaHelper
     * @param \Licentia\Equity\Model\ResourceModel\IndexFactory $indexFactory
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Equity\Model\ResourceModel\IndexFactory $indexFactory
    ) {

        $this->indexFactory = $indexFactory;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     * Add review summary info for tagged product collection
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        try {
            $bunches = $observer->getEvent()->getBunch();

            foreach ($bunches as $bunch) {
                $this->indexFactory->create()->reindexProduct($bunch['sku']);
            }
        } catch (\Exception $e) {
            $this->pandaHelper->logWarning($e);
        }

        return $this;
    }
}
