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

namespace Licentia\Equity\Block\Adminhtml\Customer\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

/**
 *
 */
class Prices extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Licentia\Equity\Model\ResourceModel\Evolutions\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollection;

    /**
     * Prices constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                               $context
     * @param \Magento\Backend\Helper\Data                                          $backendHelper
     * @param \Magento\Framework\Registry                                           $registry
     * @param \Licentia\Equity\Model\ResourceModel\CustomerPrices\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory        $productCollection
     * @param array                                                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        \Licentia\Equity\Model\ResourceModel\CustomerPrices\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        array $data = []
    ) {

        parent::__construct($context, $backendHelper, $data);

        $this->registry = $registry;
        $this->collectionFactory = $collectionFactory;
        $this->productCollection = $productCollection;
    }

    public function _construct()
    {

        parent::_construct();
        $this->setId('prices_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    /**
     * Return Tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {

        return __('Customer Prices');
    }

    /**
     * Return Tab title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {

        return __('Customer Prices');
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {

        return 'ajax';
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {

        return $this->getUrl('pandae/customerprices/pricesgrid', ['_current' => true]);

    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {

        return true;
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {

        return $this->registry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {

        return false;
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {

        $collection = $this->productCollection->create();
        $resource = $collection->getResource();
        $collection->getSelect()
                   ->joinInner(
                       ['p' => $resource->getTable('panda_customer_prices')],
                       'e.entity_id = p.product_id'
                       , ['entity_id' => 'price_id']
                   );

        if ($customerId = $this->registry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)) {
            $collection->getSelect()->where('customer_id=?', $customerId);
        }

        $collection->addAttributeToSelect('*');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {

        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'align'  => 'left',
                'index'  => 'name',
            ]
        );

        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'align'  => 'left',
                'index'  => 'sku',
            ]
        );
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'align'  => 'left',
                'index'  => 'price',
                'type'   => 'price',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {

        return $this->getUrl('pandae/customerprices/pricesgrid', ['_current' => true]);
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {

        $this->getMassactionBlock()->setTemplate('Licentia_Equity::widget/grid/massaction_extended.phtml');

        $this->getMassactionBlock()->setFormFieldName('ids');
        $this->setMassactionIdField('record_id');

        $this->getMassactionBlock()
             ->addItem(
                 'Delete',
                 [
                     'label'   => __('Delete'),
                     'url'     => $this->getUrl('pandae/customerprices/deleteprices', ['_current' => true]),
                     'confirm' => __('Are you sure?'),
                 ]
             );

        return $this;
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     *
     * @return bool
     */
    public function getRowUrl($row)
    {

        return false;
    }
}