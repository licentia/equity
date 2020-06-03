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
 * @modified   01/06/20, 16:24 GMT
 *
 */

namespace Licentia\Equity\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class CustomerLogout
 *
 * @package Licentia\Equity\Observer
 */
class CustomerLogout implements ObserverInterface
{

    /**
     * @var \Licentia\Equity\Logger\Logger
     */
    protected $pandaLogger;

    /**
     * @var \Licentia\Equity\Model\TwoFactorFactory
     */
    protected $twoFactorFactory;

    /**
     * NewLogin constructor.
     *
     * @param \Licentia\Equity\Logger\Logger          $pandaLogger
     * @param \Licentia\Equity\Model\TwoFactorFactory $twoFactorFactory
     */
    public function __construct(
        \Licentia\Equity\Logger\Logger $pandaLogger,
        \Licentia\Equity\Model\TwoFactorFactory $twoFactorFactory
    ) {

        $this->twoFactorFactory = $twoFactorFactory;
        $this->pandaLogger = $pandaLogger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     */
    public function execute(\Magento\Framework\Event\Observer $event)
    {

        try {
            $this->twoFactorFactory->create()->logoutCustomer($event);
        } catch (\Exception $e) {
            $this->pandaLogger->warning($e->getMessage());
        }
    }
}