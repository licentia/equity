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
    protected $segmentsFactory;

    /**
     * @var
     */
    protected $paramName;

    /**
     * @var array
     */
    protected $additionalData = [];

    /**
     * @var array
     */
    protected $data;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

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
                        'type'  => 'customer_segment_' . $optionCode['value'],
                        'label' => $optionCode['label'],
                    ];

                    if ($this->urlPath && $this->paramName) {
                        $this->options[$optionCode['value']]['url'] = $this->urlBuilder->getUrl(
                            $this->urlPath,
                            [$this->paramName => $optionCode['value']]
                        );
                    }

                    $this->options[$optionCode['value']] = array_merge_recursive(
                        $this->options[$optionCode['value']],
                        $this->additionalData
                    );
                }
                if (is_array($this->options)) {
                    $this->options = array_values($this->options);
                }
            }
        }

        return $this->options;
    }
}
