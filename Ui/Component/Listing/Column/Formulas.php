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

namespace Licentia\Equity\Ui\Component\Listing\Column;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Formula
 *
 * @package Licentia\Panda\Ui\Component\Listing\Column\Reports
 */
class Formulas extends Column
{

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;

    /**
     * @var \Licentia\Equity\Model\FormulasFactory
     */
    protected $formulasFactory;

    /**
     * Formula constructor.
     *
     * @param \Licentia\Equity\Model\FormulasFactory $formulasFactory
     * @param \Magento\Framework\UrlInterface        $urlInterface
     * @param ContextInterface                       $context
     * @param UiComponentFactory                     $uiComponentFactory
     * @param array                                  $components
     * @param array                                  $data
     *
     */
    public function __construct(
        \Licentia\Equity\Model\FormulasFactory $formulasFactory,
        \Magento\Framework\UrlInterface $urlInterface,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components,
        array $data
    ) {

        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->urlInterface = $urlInterface;
        $this->formulasFactory = $formulasFactory;

        $formulas = $this->formulasFactory->create()->getFormulas();

        $info = $this->getData();

        $eqName = explode('_', $info['name']);

        $info['config']['label'] = $formulas->getData('formula_' . $eqName[1] . '_name');

        $this->setData($info);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if ($this->getData('js_config/extends') == 'panda_formulas_listing' && (!empty(
                        $item[$this->getData(
                            'name'
                        )]
                        ) || !empty($item[$this->getData('name') . '_result']))) {
                    $item[$this->getData('name')] = __('Formula') . ': ' . $item[$this->getData('name')] . ' / ' . __(
                            'Result'
                        ) . ': ' . $item[$this->getData('name') . '_result'];
                }
            }
        }

        return $dataSource;
    }
}
