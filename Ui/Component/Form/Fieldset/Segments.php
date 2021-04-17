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

namespace Licentia\Equity\Ui\Component\Form\Fieldset;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Form\Fieldset;

/**
 * Class Segments Fieldset
 */
class Segments extends Fieldset
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    /**
     * Constructor
     *
     * @param ContextInterface                                   $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param UiComponentInterface[]                             $components
     * @param array                                              $data
     *
     */
    public function __construct(
        ContextInterface $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        array $components = [],
        array $data = []
    ) {

        parent::__construct($context, $components, $data);

        $this->scopeConfig = $scopeConfigInterface;
    }

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare()
    {

        parent::prepare();
        if (!$this->scopeConfig->isSetFlag('panda_magna/segments/acl')) {
            $this->_data['config']['componentDisabled'] = true;
        }
    }
}
