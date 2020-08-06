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

namespace Licentia\Equity\Observer\Admin;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class NotifyBuild
 *
 * @package Licentia\Panda\Observer
 */
class NotifyBuild implements ObserverInterface
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Licentia\Equity\Model\ResourceModel\Segments\CollectionFactory
     */
    protected $segmentsCollection;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManagerInterface;

    /**
     * NotifyBuild constructor.
     *
     * @param \Licentia\Panda\Helper\Data                                     $pandaHelper
     * @param \Magento\Backend\Model\Auth\Session                             $session
     * @param \Magento\Framework\Message\ManagerInterface                     $managerInterface
     * @param \Licentia\Equity\Model\ResourceModel\Segments\CollectionFactory $segmentsCollection
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Backend\Model\Auth\Session $session,
        \Magento\Framework\Message\ManagerInterface $managerInterface,
        \Licentia\Equity\Model\ResourceModel\Segments\CollectionFactory $segmentsCollection
    ) {

        $this->segmentsCollection = $segmentsCollection;
        $this->session = $session;
        $this->messageManagerInterface = $managerInterface;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     */
    public function execute(\Magento\Framework\Event\Observer $event)
    {

        try {
            $admin = $this->session->getUser();

            if (!$admin) {
                return;
            }

            $user = $admin->getId();

            $segments = $this->segmentsCollection->create()
                                                 ->addFieldToSelect('notify_user')
                                                 ->addFieldToSelect('segment_id')
                                                 ->addFieldToFilter('notify_user', $user)
                                                 ->addFieldToFilter('build', 0);

            if ($segments->count() == 0) {
                return;
            }

            /** @var \Licentia\Equity\Model\Segments $segment */
            foreach ($segments as $segment) {
                $segment->setData('notify_user', 0)->save();
            }

            $this->messageManagerInterface->addSuccessMessage(__('Your background segment updates have finished.'));
        } catch (\Exception $e) {
            $this->pandaHelper->logWarning($e);
        }
    }
}
