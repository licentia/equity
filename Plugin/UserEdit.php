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
            $fieldset = $form->addFieldset('panda_twofactor_number', ['legend' => __('Two-Factor Authentication')]);
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
