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

namespace Licentia\Equity\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Access controller
 */
class CustomerPrices extends Action
{

    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Licentia_Equity::customer_prices';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Licentia\Equity\Model\AccessFactory
     */
    protected $customerPricesFactory;

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */

    protected $dateFilter;

    /**
     * Access constructor.
     *
     * @param Action\Context                                    $context
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date    $dateFilter
     * @param \Magento\Store\Model\StoreManagerInterface        $storeManager
     * @param \Licentia\Equity\Helper\Data                      $helperData
     * @param \Magento\Framework\View\Result\PageFactory        $resultPageFactory
     * @param \Magento\Framework\Registry                       $registry
     * @param \Licentia\Equity\Model\AccessFactory              $customerPricesFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\LayoutFactory      $resultLayoutFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Licentia\Equity\Helper\Data $helperData,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Equity\Model\CustomerPricesFactory $customerPricesFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        $this->pandaHelper = $helperData;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->layoutFactory = $resultLayoutFactory;
        $this->customerPricesFactory = $customerPricesFactory;
        $this->storeManager = $storeManager;
        $this->dateFilter = $dateFilter;

        parent::__construct($context);
    }

    /**
     *
     */
    public function execute()
    {

    }

}
