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
 * @modified   18/03/20, 05:29 GMT
 *
 */

namespace Licentia\Equity\Controller\Adminhtml\Formulas;

use Magento\Backend\App\Action;

/**
 * Class Edit
 *
 * @package Licentia\Panda\Controller\Adminhtml\Formulas
 */
class TestFormulas extends \Licentia\Equity\Controller\Adminhtml\Formulas
{

    /**
     * @var \Licentia\Equity\Helper\Math
     */
    protected $mathHelper;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Licentia\Equity\Model\KpisFactory
     */
    protected $kpisFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * TestFormulas constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface        $storeManager
     * @param \Magento\Catalog\Api\ProductRepositoryInterface   $productRepository
     * @param \Magento\Customer\Model\CustomerFactory           $customerFactory
     * @param \Licentia\Equity\Helper\Math                      $mathHelper
     * @param \Licentia\Equity\Model\KpisFactory                $kpisFactory
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
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Licentia\Equity\Helper\Math $mathHelper,
        \Licentia\Equity\Model\KpisFactory $kpisFactory,
        \Licentia\Equity\Model\FormulasRepository $formulasRepository,
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Equity\Model\FormulasFactory $formulasFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        parent::__construct(
            $storeManager,
            $formulasRepository,
            $context,
            $resultPageFactory,
            $registry,
            $formulasFactory,
            $resultForwardFactory,
            $fileFactory,
            $resultLayoutFactory
        );

        $this->kpisFactory = $kpisFactory;
        $this->customerFactory = $customerFactory;
        $this->productRepository = $productRepository;
        $this->mathHelper = $mathHelper;
    }

    /**
     * @return $this|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        $form = [];

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParams();
            $form = $data;
            $product = false;
            try {
                $product = $this->productRepository->get($data['sku']);
            } catch (\Exception $e) {
                $form['error'] = __('Product Not Found');
            }

            if ($product) {
                /** @var \Magento\Customer\model\Customer $customer */
                $customer = $this->customerFactory->create();

                if (is_numeric($data['customer'])) {
                    $customer->load($data['customer']);
                } elseif (filter_var($data['customer'], FILTER_VALIDATE_EMAIL)) {
                    $customer->setWebsiteId(1);
                    $customer->loadByEmail($data['customer']);
                }

                if ($data['price']) {
                    $product->setPrice($data['price']);
                }

                if (!$customer->getId() && $data['customer']) {
                    $form['error'] = __('Customer Not Found');
                }
            }

            $result = false;
            if (!isset($form['error'])) {

                try {
                    $result = $this->mathHelper->getEvaluatedProductPriceExpressionTest(
                        $product,
                        $customer,
                        $data['formula']
                    );
                } catch (\Exception $e) {

                    $form['error'] = __('There is an error in your formula. Please make sure you only use variables and mathematical symbols: + - * / ( )');
                }

                if (null === $result) {
                    $form['error'] = __('Your formula is returning a null. Perhaps using stats that are empty');
                }

                if (isset($result) && !$result !== null) {
                    $form['result'] = $result['result'];

                    if (isset($result['kpis'])) {
                        $options = $this->kpisFactory->create()->getKpisDescription();
                        unset($options['email_meta']);

                        $n = [];
                        foreach ($result['kpis'] as $k => $kpi) {
                            if (isset($options[$k]) && is_numeric($kpi)) {
                                $n[$options[$k]['title'] . ' {e.' . $k . '}'] = $kpi;
                            }
                        }
                        $form['kpis'] = $n;
                    }
                }
            }
        }

        $resultPage = $this->resultPageFactory->create();

        $form['action'] = $this->getUrl('pandae/formulas/testFormulas', ['_current' => true]);

        $resultPage->addContent(
            $resultPage->getLayout()
                       ->createBlock('\Magento\Framework\View\Element\Template')
                       ->setTemplate('Licentia_Equity::formulas/test.phtml')
                       ->setData('form', $form)
        );

        $resultPage->addContent(
            $resultPage->getLayout()
                       ->createBlock('\Magento\Framework\View\Element\Template')
                       ->setTemplate('Licentia_Equity::help/pricing.phtml')
        );

        return $resultPage;
    }
}
