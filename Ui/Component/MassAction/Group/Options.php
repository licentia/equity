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

namespace Licentia\Equity\Ui\Component\MassAction\Group;

use Licentia\Equity\Model\SegmentsFactory;
use Magento\Framework\UrlInterface;

/**
 * Class Options
 *
 * @package Licentia\Panda\Ui\Component\MassAction\Group
 */
class Options implements \JsonSerializable
{

    /**
     * @var
     */
    protected $options;

    /**
     * @var SegmentsFactory
     */
    protected SegmentsFactory $segmentsFactory;

    /**
     * @var
     */
    protected $paramName;

    /**
     * @var array
     */
    protected array $additionalData = [];

    /**
     * @var array
     */
    protected array $data;

    /**
     * @var UrlInterface
     */
    protected UrlInterface $urlBuilder;

    /**
     * @var
     */
    protected $urlPath;

    /**
     * Options constructor.
     *
     * @param SegmentsFactory $segmentsFactory
     * @param UrlInterface    $urlBuilder
     * @param array           $data
     */
    public function __construct(
        SegmentsFactory $segmentsFactory,
        UrlInterface $urlBuilder,
        array $data = []
    ) {

        $this->segmentsFactory = $segmentsFactory;
        $this->data = $data;
        $this->urlBuilder = $urlBuilder;
    }

    protected function prepareData()
    {

        foreach ($this->data as $key => $value) {
            switch ($key) {
                case 'urlPath':
                    $this->urlPath = $value;
                    break;
                case 'paramName':
                    $this->paramName = $value;
                    break;
                default:
                    $this->additionalData[$key] = $value;
                    break;
            }
        }
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {

        if ($this->options === null) {
            $options = $this->segmentsFactory->create()->getOptionArray(false);
            if ($options) {
                $this->prepareData();
                foreach ($options as $optionCode) {
                    $this->options[$optionCode['value']] = [
                        'type'          => 'customer_segment_' . $optionCode['value'],
                        'label'         => $optionCode['label'],
                        '__disableTmpl' => true,
                    ];

                    $this->options[$optionCode['value']]['url'] = $this->urlBuilder->getUrl(
                        'pandae/segments/massManualAddCustomer',
                        ['group' => $optionCode['value']]
                    );

                    $this->options[$optionCode['value']] = array_merge_recursive(
                        $this->options[$optionCode['value']],
                        $this->additionalData
                    );
                }

                $this->options = array_values($this->options);

            }
        }

        return $this->options;
    }
}
