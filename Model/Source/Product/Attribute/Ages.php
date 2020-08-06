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

namespace Licentia\Equity\Model\Source\Product\Attribute;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 */
class Ages extends AbstractSource implements SourceInterface, OptionSourceInterface
{

    /**#@+
     *
     * /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {

        return [
            'ignore' => __('Ignore'),
            '18-24'  => __('18-24'),
            '25-34'  => __('25-34'),
            '35-44'  => __('35-44'),
            '45-54'  => __('45-54'),
            '55-64'  => __('55-64'),
            '65+'    => __('65+'),
        ];
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {

        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }
}
