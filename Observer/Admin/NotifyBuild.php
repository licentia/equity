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
 * @modified   03/06/20, 16:19 GMT
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
