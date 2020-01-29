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
     * @var \Licentia\Equity\Logger\Logger
     */
    protected $pandaLogger;

    /**
     * @var \Licentia\Equity\Model\SegmentsFactory
     */
    protected $segmentsFactory;

    /**
     * UpdateRealTimeSegments constructor.
     *
     * @param \Licentia\Equity\Model\SegmentsFactory             $segmentsFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Licentia\Equity\Logger\Logger                     $pandaLogger
     */
    public function __construct(
        \Licentia\Equity\Model\SegmentsFactory $segmentsFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Licentia\Equity\Logger\Logger $pandaLogger
    ) {

        $this->segmentsFactory = $segmentsFactory;
        $this->scopeConfig = $scopeConfigInterface;
        $this->pandaLogger = $pandaLogger;
    }

    /**
     * @return bool|void
     */
    public function execute()
    {

        try {
            return $this->segmentsFactory->create()->updateRealTimeCron();
        } catch (\Exception $e) {
            $this->pandaLogger->warning($e->getMessage());
        }

        return true;
    }
}
