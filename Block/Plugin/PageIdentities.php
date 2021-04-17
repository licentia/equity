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
