<?php
/*
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
