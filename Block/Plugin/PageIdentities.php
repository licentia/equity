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

namespace Licentia\Equity\Block\Plugin;

use Magento\Customer\Model\Session;

/**
 * Class PageIdentities
 *
 * @package Licentia\Panda\Block\Plugin
 */
class PageIdentities
{

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scope;

    /**
     * PriceBoxTags constructor.
     *
     * @param \Licentia\Equity\Helper\Data                       $pandaHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param Session                                            $customerSession
     */
    public function __construct(
        \Licentia\Equity\Helper\Data $pandaHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        Session $customerSession
    ) {

        $this->pandaHelper = $pandaHelper;
        $this->customerSession = $customerSession;
        $this->scope = $scopeConfigInterface;
    }

    /**
     * @param \Magento\Cms\Model\Page $subject
     * @param string                  $result
     *
     * @return string
     */
    public function afterGetIdentities(\Magento\Cms\Model\Page $subject, $result)
    {

        $cacheKey = [];
        $cacheKey[] = 'panda';

        if (is_array($result)) {
            return array_merge($result, $cacheKey);
        }

        return $result . '-' . implode('-', $cacheKey);
    }
}
