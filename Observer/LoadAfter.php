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
use Magento\Store\Model\ScopeInterface;

/**
 * Class LoadAfter
 *
 * @package Licentia\Panda\Observer
 */
class LoadAfter implements ObserverInterface
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
     * LoadAfter constructor.
     *
     * @param \Licentia\Panda\Helper\Data                        $pandaHelper
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManagerInterface
     * @param \Licentia\Equity\Model\AccessFactory               $accessFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Licentia\Equity\Model\AccessFactory $accessFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
    ) {

        $this->scopeConfig = $scopeConfigInterface;
        $this->accessFactory = $accessFactory;
        $this->storeManager = $storeManagerInterface;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     *
     * @return bool|void
     */
    public function execute(\Magento\Framework\Event\Observer $event)
    {

        if (!$this->scopeConfig->isSetFlag('panda_magna/segments/acl', ScopeInterface::SCOPE_WEBSITE)) {
            return;
        }

        try {
            $model = $event->getObject();
            $type = null;
            $ok = null;

            if (!($model instanceof \Magento\Cms\Model\Page) &&
                !($model instanceof \Magento\Cms\Model\Block)) {
                return true;
            }

            if ($model instanceof \Magento\Cms\Model\Page) {
                $type = 'page_id';
                $ok = $this->accessFactory->create()->checkAccess($model->getId(), 'page');
            }

            if ($model instanceof \Magento\Cms\Model\Block) {
                $type = 'block_id';
                $ok = $this->accessFactory->create()->checkAccess($model->getId(), 'block');
            }

            if (!$ok && $type != 'block_id') {
                $baseUrl = $this->storeManager->getStore()->getBaseUrl();

                header('LOCATION: ' . $baseUrl);

                return true;
            } elseif (!$ok) {
                $model->setData([]);
            }
        } catch (\Exception $e) {
            $this->pandaHelper->logWarning($e);
        }
    }
}
