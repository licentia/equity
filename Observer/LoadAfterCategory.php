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
 * @modified   03/06/20, 16:19 GMT
 *
 */

namespace Licentia\Equity\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class LoadAfter
 *
 * @package Licentia\Panda\Observer
 */
class LoadAfterCategory implements ObserverInterface
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

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
     * @var \Magento\Framework\App\ResponseFactory
     */
    private $responseFactory;

    /**
     * LoadAfterCategory constructor.
     *
     * @param \Magento\Framework\App\ResponseFactory             $responseFactory
     * @param \Licentia\Panda\Helper\Data                        $pandaHelper
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManagerInterface
     * @param \Licentia\Equity\Model\AccessFactory               $accessFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     */
    public function __construct(
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Licentia\Equity\Model\AccessFactory $accessFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
    ) {

        $this->scopeConfig = $scopeConfigInterface;
        $this->accessFactory = $accessFactory;
        $this->storeManager = $storeManagerInterface;
        $this->pandaHelper = $pandaHelper;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     *
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $event)
    {

        if ($this->scopeConfig->getValue(
            'panda_magna/segments/acl',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        )) {
            try {
                $model = $event->getEvent()->getDataObject();

                $categoryAccess = $this->accessFactory->create()->checkAccess($model->getId(), 'category');

                if (!$categoryAccess) {
                    $this->responseFactory->create()
                                          ->setRedirect($this->storeManager->getStore()->getBaseUrl())
                                          ->sendResponse();

                    return $this;
                } elseif (!$categoryAccess) {
                    $model->setData([]);
                }
            } catch (\Exception $e) {
                $this->pandaHelper->logWarning($e);
            }
        }
    }
}
