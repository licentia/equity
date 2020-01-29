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

namespace Licentia\Equity\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class LoadBeforeProductCollection
 *
 * @package Licentia\Panda\Observer
 */
class LoadBeforeProductCollection implements ObserverInterface
{

    /**
     * @var \Licentia\Equity\Logger\Logger
     */
    protected $pandaLogger;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Licentia\Equity\Model\AccessFactory
     */
    protected $accessFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * LoadAfter constructor.
     *
     * @param \Licentia\Equity\Logger\Logger                     $pandaLogger
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManagerInterface
     * @param \Licentia\Equity\Model\AccessFactory               $accessFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     */
    function __construct(
        \Licentia\Equity\Logger\Logger $pandaLogger,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Licentia\Equity\Model\AccessFactory $accessFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
    ) {

        $this->scopeConfig = $scopeConfigInterface;
        $this->accessFactory = $accessFactory;
        $this->storeManager = $storeManagerInterface;
        $this->pandaLogger = $pandaLogger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     */
    public function execute(\Magento\Framework\Event\Observer $event)
    {

        if (!$this->scopeConfig->getValue(
            'panda_magna/segments/acl',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        )) {
            return;
        }
        try {
            $model = $event->getEvent()->getCollection();

            $this->accessFactory->create()->getLockedEntities($model, 'product');
        } catch (\Exception $e) {
            $this->pandaLogger->warning($e->getMessage());
        }
    }
}
