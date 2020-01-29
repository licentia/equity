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
    protected $scopeConfig;

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
