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

namespace Licentia\Equity\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Class Formulas
 *
 * @package Licentia\Panda\Controller\Adminhtml
 */
class Formulas extends Action
{

    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Licentia_Equity::formulas';

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
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Licentia\Equity\Model\FormulasFactory
     */
    protected $formulasFactory;

    /**
     * @var \Licentia\Equity\Model\FormulasRepository
     */
    protected $formulasRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Formulas constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface        $storeManager
     * @param \Licentia\Equity\Model\FormulasRepository         $formulasRepository
     * @param Action\Context                                    $context
     * @param \Magento\Framework\View\Result\PageFactory        $resultPageFactory
     * @param \Magento\Framework\Registry                       $registry
     * @param \Licentia\Equity\Model\FormulasFactory            $formulasFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory  $fileFactory
     * @param \Magento\Framework\View\Result\LayoutFactory      $resultLayoutFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Licentia\Equity\Model\FormulasRepository $formulasRepository,
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Equity\Model\FormulasFactory $formulasFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        $this->storeManager = $storeManager;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->fileFactory = $fileFactory;
        $this->layoutFactory = $resultLayoutFactory;
        $this->formulasFactory = $formulasFactory;
        $this->formulasRepository = $formulasRepository;

        parent::__construct($context);
    }

    /**
     *
     */
    public function execute()
    {

        $model = $this->formulasFactory->create()->getFormulas();

        if ($data = $this->_getSession()->getFormData(true)) {
            $model->addData($data);
        }

        /*

        $websites = $this->_storeManager->getWebsites();

        foreach ($websites as $website) {
            $collection = $this->_formulasFactory->create()
                                                  ->getCollection()->addFieldToFilter('website_id', $website->getId());

            if ($collection->count() == 0) {
                $this->_formulasFactory->create()
                                        ->setData(
                                            [
                                                'website_id'       => $website->getId(),
                                                'formula_0_name'  => 'Customer Loyalty',
                                                'formula_1_name'  => 'Formula 1',
                                                'formula_2_name'  => 'Formula 2',
                                                'formula_3_name'  => 'Formula 3',
                                                'formula_4_name'  => 'Formula 4',
                                                'formula_5_name'  => 'Formula 5',
                                                'formula_6_name'  => 'Formula 6',
                                                'formula_7_name'  => 'Formula 7',
                                                'formula_8_name'  => 'Formula 8',
                                                'formula_9_name'  => 'Formula 9',
                                                'formula_10_name' => 'Formula 10',
                                            ]
                                        )
                                        ->save();
            }
        }

        */

        $this->registry->register('panda_formula', $model, true);
    }

}
