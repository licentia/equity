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

namespace Licentia\Equity\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Directory\Helper\Data;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Field;

/**
 * Class SegmentsAccess
 *
 * @package Licentia\Panda\Ui\DataProvider\Product\Form\Modifier
 */
class SegmentsAccess extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{

    /**
     * @var Data
     */
    protected $directoryHelper;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var string
     */
    protected $scopeName;

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @var \Licentia\Equity\Model\ResourceModel\Segments\CollectionFactory
     */
    protected $segmentsCollection;

    /**
     * @param \Licentia\Equity\Model\ResourceModel\Segments\CollectionFactory $segmentsCollection
     * @param LocatorInterface                                                $locator
     * @param StoreManagerInterface                                           $storeManager
     * @param Data                                                            $directoryHelper
     * @param ArrayManager                                                    $arrayManager
     * @param string                                                          $scopeName
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Licentia\Equity\Model\ResourceModel\Segments\CollectionFactory $segmentsCollection,
        LocatorInterface $locator,
        StoreManagerInterface $storeManager,
        Data $directoryHelper,
        ArrayManager $arrayManager,
        $scopeName = ''
    ) {

        $this->locator = $locator;
        $this->storeManager = $storeManager;
        $this->arrayManager = $arrayManager;
        $this->scopeName = $scopeName;
        $this->directoryHelper = $directoryHelper;
        $this->segmentsCollection = $segmentsCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {

        $this->meta = $meta;

        $this->customizeTierPrice();

        return $this->meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {

        return $data;
    }

    /**
     * Customize tier price field
     *
     * @return $this
     */
    protected function customizeTierPrice()
    {

        $tierPricePath = $this->arrayManager->findPath('panda_access', $this->meta, null, 'children');

        if ($tierPricePath) {
            $this->meta = $this->arrayManager->merge(
                $tierPricePath,
                $this->meta,
                $this->getTierPriceStructure($tierPricePath)
            );
            $this->meta = $this->arrayManager->set(
                $this->arrayManager->slicePath($tierPricePath, 0, -3)
                . '/' . 'panda_access',
                $this->meta,
                $this->arrayManager->get($tierPricePath, $this->meta)
            );
            $this->meta = $this->arrayManager->remove(
                $this->arrayManager->slicePath($tierPricePath, 0, -2),
                $this->meta
            );
        }

        return $this;
    }

    /**
     * Retrieve allowed customer groups
     *
     * @return array
     */
    protected function getSegments()
    {

        $segments = [];
        $collection = $this->segmentsCollection->create()->setOrder('name', 'ASC');
        foreach ($collection as $segment) {
            $segments[] = [
                'label' => $segment->getName(),
                'value' => $segment->getId(),
            ];
        }

        return $segments;
    }

    /**
     * Get tier price dynamic rows structure
     *
     * @param string $tierPricePath
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getTierPriceStructure($tierPricePath)
    {

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType'       => 'dynamicRows',
                        'label'               => __('Segments Access'),
                        'renderDefaultRecord' => false,
                        'recordTemplate'      => 'record',
                        'dataScope'           => '',
                        'dndConfig'           => [
                            'enabled' => false,
                        ],
                        'disabled'            => false,
                        'sortOrder'           =>
                            $this->arrayManager->get($tierPricePath . '/arguments/data/config/sortOrder', $this->meta),
                    ],
                ],
            ],
            'children'  => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'isTemplate'    => true,
                                'is_collection' => true,
                                'component'     => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope'     => '',
                            ],
                        ],
                    ],
                    'children'  => [
                        'segment_id'   => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'dataType'      => Text::NAME,
                                        'formElement'   => Select::NAME,
                                        'componentType' => Field::NAME,
                                        'dataScope'     => 'segment_id',
                                        'label'         => __('Segment'),
                                        'options'       => $this->getSegments(),
                                        'visible'       => true,
                                        'disabled'      => false,
                                    ],
                                ],
                            ],
                        ],
                        'actionDelete' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => 'actionDelete',
                                        'dataType'      => Text::NAME,
                                        'label'         => '',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
