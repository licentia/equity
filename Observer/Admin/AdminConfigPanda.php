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
 * Class AdminConfigPanda
 *
 * @package Licentia\Panda\Observer
 */
class AdminConfigPanda implements ObserverInterface
{

    /**
     * @var \Licentia\Equity\Logger\Logger
     */
    protected $pandaLogger;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Licentia\Panda\Helper\DomHelper
     */
    protected $domHelper;

    /**
     * @var \Magento\Eav\Model\Entity\AttributeFactory
     */
    protected $attributeFactory;

    /**
     * AdminConfigPanda constructor.
     *
     * @param \Licentia\Equity\Logger\Logger                     $pandaLogger
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\RequestInterface            $request
     * @param \Magento\Eav\Model\Entity\AttributeFactory         $attributeFactory
     * @param \Licentia\Panda\Helper\DomHelper                   $domHelper
     */
    public function __construct(
        \Licentia\Equity\Logger\Logger $pandaLogger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory,
        \Licentia\Panda\Helper\DomHelper $domHelper
    ) {

        $this->request = $request;
        $this->domHelper = $domHelper;
        $this->scopeConfig = $scopeConfig;
        $this->pandaLogger = $pandaLogger;
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     */
    public function execute(\Magento\Framework\Event\Observer $event)
    {

        try {
            if ($event->getEvent()->getName() == 'admin_system_config_changed_section_panda_magna') {
                $enabled = $this->scopeConfig->isSetFlag('panda_magna/prices/enabled');

                $attribute = $this->attributeFactory->create()->loadByCode('catalog_product', 'panda_segments');
                if (!$enabled) {
                    $attribute->setData('is_visible', 0)->save();
                } else {
                    $attribute->setData('is_visible', 1)->save();
                }
            }
        } catch (\Exception $e) {
            $this->pandaLogger->warning($e->getMessage());
        }
    }
}
