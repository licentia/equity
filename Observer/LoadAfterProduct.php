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

namespace Licentia\Equity\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Store\Model\ScopeInterface;

/**
 * Class LoadAfter
 *
 * @package Licentia\Panda\Observer
 */
class LoadAfterProduct implements ObserverInterface
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected \Licentia\Panda\Helper\Data $pandaHelper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected \Magento\Framework\App\RequestInterface $request;

    /**
     * @var \Licentia\Equity\Model\AccessFactory
     */
    protected \Licentia\Equity\Model\AccessFactory $accessFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected \Magento\Store\Model\StoreManagerInterface $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    /**
     * @var \Licentia\Equity\Model\Segments\ProductsFactory
     */
    protected \Licentia\Equity\Model\Segments\ProductsFactory $productsFactory;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected \Magento\Framework\Controller\Result\ForwardFactory $_forwardFactory;

    /**
     * LoadAfter constructor.
     *
     * @param \Licentia\Panda\Helper\Data                         $pandaHelper
     * @param \Magento\Framework\App\RequestInterface             $request
     * @param \Magento\Store\Model\StoreManagerInterface          $storeManagerInterface
     * @param \Licentia\Equity\Model\AccessFactory                $accessFactory
     * @param \Licentia\Equity\Model\Segments\ProductsFactory     $productsFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface  $scopeConfigInterface
     * @param \Magento\Framework\Controller\Result\ForwardFactory $forwardFactory
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Licentia\Equity\Model\AccessFactory $accessFactory,
        \Licentia\Equity\Model\Segments\ProductsFactory $productsFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Framework\Controller\Result\ForwardFactory $forwardFactory
    ) {

        $this->scopeConfig = $scopeConfigInterface;
        $this->storeManager = $storeManagerInterface;
        $this->accessFactory = $accessFactory;
        $this->request = $request;
        $this->pandaHelper = $pandaHelper;
        $this->productsFactory = $productsFactory;
        $this->_forwardFactory = $forwardFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     *
     * @return \Magento\Framework\Controller\Result\Forward
     */
    public function execute(\Magento\Framework\Event\Observer $event)
    {

        if (!$this->scopeConfig->getValue('panda_magna/segments/acl', ScopeInterface::SCOPE_WEBSITE) &&
            !$this->scopeConfig->getValue('panda_magna/catalogs/enabled', ScopeInterface::SCOPE_WEBSITE)) {
            return;
        }
        try {

            if ($this->scopeConfig->getValue('panda_magna/segments/acl', ScopeInterface::SCOPE_WEBSITE)) {

                $model = $event->getEvent()->getDataObject();
                $access = $this->accessFactory->create();

                $okCat = true;
                $okProd = true;
                if ($this->request->getModuleName() != 'customer' &&
                    $this->request->getModuleName() != 'sales') {

                    $okCat = $access->checkAccess($model->getCategoryIds(), 'category');

                    if ($this->scopeConfig->isSetFlag('panda_magna/catalogs/enabled', ScopeInterface::SCOPE_WEBSITE)) {

                        $okProd = $this->productsFactory->create()
                                                        ->canAccessProductCatalog($model->getId());
                    }

                }

                $ok = $access->checkAccess($model->getSku(), 'product');

                if (!$ok || !$okCat || !$okProd) {
                    return $this->_forwardFactory->create()->forward('defaultNoRoute');
                } elseif (!$ok) {
                    $model->setData([]);
                }
            }

        } catch (\Exception $e) {
            $this->pandaHelper->logWarning($e);
        }
    }
}
