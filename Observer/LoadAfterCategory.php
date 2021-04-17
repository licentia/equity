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
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $_forwardFactory;

    /**
     * LoadAfterCategory constructor.
     *
     * @param \Licentia\Panda\Helper\Data                         $pandaHelper
     * @param \Magento\Store\Model\StoreManagerInterface          $storeManagerInterface
     * @param \Licentia\Equity\Model\AccessFactory                $accessFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface  $scopeConfigInterface
     * @param \Magento\Framework\Controller\Result\ForwardFactory $forwardFactory
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Licentia\Equity\Model\AccessFactory $accessFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Framework\Controller\Result\ForwardFactory $forwardFactory
    ) {

        $this->scopeConfig = $scopeConfigInterface;
        $this->accessFactory = $accessFactory;
        $this->storeManager = $storeManagerInterface;
        $this->pandaHelper = $pandaHelper;
        $this->_forwardFactory = $forwardFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     *
     * @return \Magento\Framework\Controller\Result\Forward
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
                    return $this->_forwardFactory->create()->forward('defaultNoRoute');
                } elseif (!$categoryAccess) {
                    $model->setData([]);
                }
            } catch (\Exception $e) {
                $this->pandaHelper->logWarning($e);
            }
        }
    }
}
