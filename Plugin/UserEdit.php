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
 * @modified   03/06/20, 19:35 GMT
 *
 */

namespace Licentia\Equity\Plugin;

/**
 * Class UserEdit
 *
 * @package Licentia\Panda\Plugin
 */
class UserEdit
{

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $userSession;

    /**
     * UserEdit constructor.
     *
     * @param \Magento\Backend\Model\Auth\Session $userSession
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $userSession
    ) {

        $this->userSession = $userSession;
    }

    /**
     * @param \Magento\User\Block\User\Edit\Tab\Main $subject
     * @param \Closure                               $proceed
     *
     * @return mixed
     */
    public function aroundGetFormHtml(
        \Magento\Backend\Block\System\Account\Edit\Form $subject,
        \Closure $proceed
    ) {

        $form = $subject->getForm();
        if (is_object($form)) {
            $fieldset = $form->addFieldset('panda_twofactor_nusmber', ['legend' => __('Two-Factor Authentication')]);
            $fieldset->addField(
                'panda_twofactor_number',
                'text',
                [
                    'name'     => 'panda_twofactor_number',
                    'label'    => __('User Mobile Phone Number'),
                    'id'       => 'panda_twofactor_number',
                    'title'    => __('User Mobile Phone Number'),
                    'required' => false,
                    'note'     => 'Please use the following format: CountryCode-CellphoneNumber. Ex: 351-989647542',
                ]
            );

            $form->addValues([
                'panda_twofactor_number' => $this->userSession->getUser()
                                                              ->getData('panda_twofactor_number'),
            ]);

            $subject->setForm($form);
        }

        return $proceed();
    }
}
