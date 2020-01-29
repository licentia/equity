<?php
/**
 * Copyright (C) 2020 Licentia, Unipessoal LDA
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @title      Licentia Panda - Magento® Sales Automation Extension
 * @package    Licentia
 * @author     Bento Vilas Boas <bento@licentia.pt>
 * @copyright  Copyright (c) Licentia - https://licentia.pt
 * @license    GNU General Public License V3
 * @modified   29/01/20, 15:22 GMT
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
     * @var \Licentia\Equity\Logger\Logger
     */
    protected $pandaLogger;

    /**
     * ReindexAll constructor.
     *
     * @param \Licentia\Equity\Logger\Logger                    $pandaLogger
     * @param \Licentia\Equity\Model\ResourceModel\IndexFactory $indexFactory
     */
    public function __construct(
        \Licentia\Equity\Logger\Logger $pandaLogger,
        \Licentia\Equity\Model\ResourceModel\IndexFactory $indexFactory
    ) {

        $this->indexFactory = $indexFactory;
        $this->pandaLogger = $pandaLogger;
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
            $this->pandaLogger->warning($e->getMessage());
        }

        return $this;
    }
}
