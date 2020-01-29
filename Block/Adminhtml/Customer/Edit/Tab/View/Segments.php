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
            'panda_magna/kpis/' . $kpi,
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
