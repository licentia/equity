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
 * Class UpdateSegmentsEvolutions
 *
 * @package Licentia\Panda\Cron
 */
class UpdateSegmentsEvolutions
{

    /**
     * @var \Licentia\Equity\Logger\Logger
     */
    protected $pandaLogger;

    /**
     * @var  \Licentia\Equity\Model\ResourceModel\Evolutions
     */
    protected $evolutionsResource;

    /**
     * UpdateSegmentsEvolutions constructor.
     *
     * @param \Licentia\Equity\Logger\Logger                  $pandaLogger
     * @param \Licentia\Equity\Model\ResourceModel\Evolutions $evolutionsResource
     */
    public function __construct(
        \Licentia\Equity\Logger\Logger $pandaLogger,
        \Licentia\Equity\Model\ResourceModel\Evolutions $evolutionsResource
    ) {

        $this->evolutionsResource = $evolutionsResource;
        $this->pandaLogger = $pandaLogger;
    }

    /**
     *
     */
    public function execute()
    {

        try {
            $this->evolutionsResource->updateEvolutions();
        } catch (\Exception $e) {
            $this->pandaLogger->warning($e->getMessage());
        }
    }
}
