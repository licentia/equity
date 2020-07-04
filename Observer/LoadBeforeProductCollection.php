<?php
/**
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

namespace Licentia\Equity\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Store\Model\ScopeInterface;

/**
 * Class LoadBeforeProductCollection
 *
 * @package Licentia\Panda\Observer
 */
class LoadBeforeProductCollection implements ObserverInterface
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
     * @var \Licentia\Equity\Model\Segments\ProductsFactory
     */
    protected $productsFactory;

    /**
     * LoadBeforeProductCollection constructor.
     *
     * @param \Licentia\Panda\Helper\Data                        $pandaHelper
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManagerInterface
     * @param \Licentia\Equity\Model\AccessFactory               $accessFactory
     * @param \Licentia\Equity\Model\Segments\ProductsFactory    $productsFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     */
    function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Licentia\Equity\Model\AccessFactory $accessFactory,
        \Licentia\Equity\Model\Segments\ProductsFactory $productsFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
    ) {

        $this->scopeConfig = $scopeConfigInterface;
        $this->accessFactory = $accessFactory;
        $this->productsFactory = $productsFactory;
        $this->storeManager = $storeManagerInterface;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     */
    public function execute(\Magento\Framework\Event\Observer $event)
    {

        if (!$this->scopeConfig->getValue('panda_magna/segments/acl', ScopeInterface::SCOPE_WEBSITE) &&
            !$this->scopeConfig->getValue('panda_magna/catalogs/enabled', ScopeInterface::SCOPE_WEBSITE)) {
            return;
        }

        try {
            $model = $event->getEvent()->getCollection();

            if (!$this->scopeConfig->getValue('panda_magna/segments/acl', ScopeInterface::SCOPE_WEBSITE)) {
                $this->accessFactory->create()->getLockedEntities($model, 'product');
            }

            if (!$this->scopeConfig->getValue('panda_magna/catalogs/enabled', ScopeInterface::SCOPE_WEBSITE)) {
                $this->productsFactory->create()->addToCollection($model);
            }

        } catch (\Exception $e) {
            $this->pandaHelper->logWarning($e);
        }
    }
}
