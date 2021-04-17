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

namespace Licentia\Equity\Block;

use Magento\Framework\View\Element\Template\Context;
use Licentia\Equity\Model\Customer\Context as CustomerContex;

class Customer extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected \Magento\Framework\App\Http\Context $httpContext;

    /**
     * @param Context                             $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array                               $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {

        $this->httpContext = $httpContext;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed|null
     */
    public function getCustomerId()
    {

        return $this->httpContext->getValue(CustomerContex::CONTEXT_CUSTOMER_ID);
    }
}