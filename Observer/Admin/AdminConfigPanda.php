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
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

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
     * @param \Licentia\Panda\Helper\Data                        $pandaHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\RequestInterface            $request
     * @param \Magento\Eav\Model\Entity\AttributeFactory         $attributeFactory
     * @param \Licentia\Panda\Helper\DomHelper                   $domHelper
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory,
        \Licentia\Panda\Helper\DomHelper $domHelper
    ) {

        $this->request = $request;
        $this->domHelper = $domHelper;
        $this->scopeConfig = $scopeConfig;
        $this->pandaHelper = $pandaHelper;
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
            $this->pandaHelper->logWarning($e);
        }
    }
}
