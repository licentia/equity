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
 * @modified   26/03/20, 20:05 GMT
 *
 */

namespace Licentia\Equity\Observer\Admin;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class AdminConfigPricingPanda
 *
 * @package Licentia\Equity\Observer\Admin
 */
class AdminConfigPricingPanda implements ObserverInterface
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * AdminConfigPricingPanda constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {

        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $event)
    {

        if ($event->getEvent()->getName() == 'admin_system_config_changed_section_panda_prices') {

            if ($this->scopeConfig->isSetFlag('panda_prices/products/enabled')) {

                $formula = $this->scopeConfig->getValue('panda_prices/products/price');

                if (stripos($formula, '{e.') !== false || stripos($formula, '{c.') !== false) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Customer variables cannot be used in the "Product Prices" section. Use the "Customer Prices" section'));
                }

                if (stripos($formula, '{') === false && stripos($formula, '}') === false) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('A variable is required in your pricing formula. Eg: {p.price}'));
                }

            }
            if ($this->scopeConfig->isSetFlag('panda_prices/customers/enabled')) {

                $formula = $this->scopeConfig->getValue('panda_prices/customers/price');

                if (stripos($formula, '{') === false && stripos($formula, '}') === false) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('A variable is required in your pricing formula. Eg: {p.price}'));
                }

            }

        }

    }
}
