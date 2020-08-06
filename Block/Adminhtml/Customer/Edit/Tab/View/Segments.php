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

namespace Licentia\Equity\Block\Adminhtml\Customer\Edit\Tab\View;

/**
 * Class Segments
 *
 * @package Licentia\Panda\Block\Adminhtml\Customer\Edit\Tab\View
 */
class Segments extends \Magento\Backend\Block\Template
{

    /**
     * @var \Licentia\Equity\Model\Segments\ListSegmentsFactory
     */
    protected $listSegmentsFactory;

    /**
     * @var \Licentia\Equity\Model\SegmentsFactory
     */
    protected $segmentsFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Licentia\Equity\Model\KpisFactory
     */
    protected $kpisFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context             $context
     * @param \Magento\Framework\Registry                         $registry
     * @param \Licentia\Equity\Model\Segments\ListSegmentsFactory $listSegmentsFactory
     * @param \Licentia\Equity\Model\SegmentsFactory              $segmentsFactory
     * @param \Licentia\Equity\Model\KpisFactory                  $kpisFactory
     * @param array                                               $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Licentia\Equity\Model\Segments\ListSegmentsFactory $listSegmentsFactory,
        \Licentia\Equity\Model\SegmentsFactory $segmentsFactory,
        \Licentia\Equity\Model\KpisFactory $kpisFactory,
        array $data = []
    ) {

        parent::__construct($context, $data);

        $this->registry = $registry;
        $this->kpisFactory = $kpisFactory;
        $this->listSegmentsFactory = $listSegmentsFactory;
        $this->segmentsFactory = $segmentsFactory;
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {

        return $this->registry->registry('current_customer');
    }

    /**
     * @return mixed
     */
    public function getCustomerSegments()
    {

        if (!$this->_scopeConfig->isSetFlag('panda_segments/customer/enabled')) {
            return false;
        }

        $customerId = $this->registry->registry('current_customer_id');

        return $this->listSegmentsFactory->create()->getCustomerSegments($customerId);
    }

    /**
     * @param $kpi
     *
     * @return string
     */
    public function getKpiComment($kpi)
    {

        return (string ) $this->_scopeConfig->getValue(
            'panda_equity/kpis_desc/' . $kpi,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return mixed
     */
    public function getCustomerKpis()
    {

        if (!$this->_scopeConfig->isSetFlag('panda_equity/kpis/enabled')) {
            return false;
        }

        $kpisWanted = explode(
            ',',
            $this->_scopeConfig->getValue(
                'panda_equity/kpis/list',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            )
        );
        array_unshift($kpisWanted, 'loyal');

        $customerId = $this->registry->registry('current_customer_id');

        /** @var \Licentia\Equity\Model\Kpis $kpis */
        $kpis = $this->kpisFactory->create()->load($customerId, 'customer_id');

        $date = [
            'last_order',
            'first_order',
            'last_activity',
            'abandoned',
            'pending_payment',
            'account',
            'last_review',
        ];

        if (!empty($kpisWanted)) {
            foreach (array_keys($kpis->getData()) as $key) {
                if (!in_array($key, $kpisWanted)) {
                    $kpis->unsetData($key);
                }

                if (in_array($key, $date) &&
                    $kpis->getData($key . '_date') &&
                    $kpis->getData($key . '_date') != '0000-00-00'
                ) {
                    $start_date = new \DateTime($kpis->getData($key . '_date'));
                    $end_date = new \DateTime();

                    $dd = date_diff($start_date, $end_date);

                    $kpis->setData($key, $dd->y . "y " . $dd->m . "m and " . $dd->d . "d");
                }
            }
        }

        return $kpis;
    }

    /**
     * @param $segmentId
     *
     * @return string
     */
    public function getSegmentName($segmentId)
    {

        return $this->segmentsFactory->create()
                                     ->load($segmentId)
                                     ->getName();
    }
}
