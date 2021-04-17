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

namespace Licentia\Equity\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class KpisActions
 *
 * @package Licentia\Panda\Ui\Component\Listing\Column
 */
class KpisActions extends Column
{

    const URL_PATH_EDIT = 'customer/index/edit';

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;

    /**
     * Campaigns constructor.
     *
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param ContextInterface                $context
     * @param UiComponentFactory              $uiComponentFactory
     * @param array                           $components
     * @param array                           $data
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlInterface,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components,
        array $data
    ) {

        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->urlInterface = $urlInterface;
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
                if (isset($item['customer_id']) && $item['customer_id'] > 0) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href'  => $this->urlInterface->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    'id' => $item['customer_id'],
                                ]
                            ),
                            'label' => __('View'),
                        ],
                    ];
                }
            }
        }

        return $dataSource;
    }
}
