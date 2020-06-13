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
class Coupons extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
{

    /**
     * @var \Licentia\Panda\Model\CouponsFactory
     */
    protected $couponsFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Licentia\Panda\Model\CouponsFactory    $couponsFactory
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Licentia\Panda\Model\CouponsFactory $couponsFactory,
        array $data = []
    ) {

        $this->couponsFactory = $couponsFactory;
        parent::__construct($context, $registry, $formFactory, $data);

        $this->setTemplate('customer/tab/coupons.phtml');
    }

    /**
     * Return Tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {

        return __('Customer Coupons');
    }

    /**
     * Return Tab title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {

        return __('Customer Coupons');
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {

        return '';
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {

        return '';
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {

        return false;
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {

        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
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
     */
    public function getCoupons()
    {

        if (!$this->canShowTab()) {
            return $this;
        }

        $resource = $this->couponsFactory->create();

        return $resource->getUserCoupons($this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID));
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {

        if ($this->canShowTab()) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
