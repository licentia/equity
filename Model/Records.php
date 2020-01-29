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

namespace Licentia\Equity\Model;

use Licentia\Equity\Api\Data\RecordsInterface;

/**
 * Class Records
 *
 * @package Licentia\Panda\Model
 */
class Records extends \Magento\Framework\Model\AbstractModel implements RecordsInterface
{

    /**
     * @return void
     */
    protected function _construct()
    {

        $this->_init(\Licentia\Equity\Model\ResourceModel\Records::class);
    }

    /**
     * Get record_id
     *
     * @return string
     */
    public function getRecordId()
    {

        return $this->getData(self::RECORD_ID);
    }

    /**
     * Set record_id
     *
     * @param string $record_id
     *
     * @return Records
     */
    public function setRecordId($record_id)
    {

        return $this->setData(self::RECORD_ID, $record_id);
    }

    /**
     * Get segment_id
     *
     * @return string
     */
    public function getSegmentId()
    {

        return $this->getData(self::SEGMENT_ID);
    }

    /**
     * Set segment_id
     *
     * @param string $segment_id
     *
     * @return Records
     */
    public function setSegmentId($segment_id)
    {

        return $this->setData(self::SEGMENT_ID, $segment_id);
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {

        return $this->getData(self::FIRSTNAME);
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Records
     */
    public function setFirstname($firstname)
    {

        return $this->setData(self::FIRSTNAME, $firstname);
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {

        return $this->getData(self::LASTNAME);
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Records
     */
    public function setLastname($lastname)
    {

        return $this->setData(self::LASTNAME, $lastname);
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {

        return $this->getData(self::EMAIL);
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Records
     */
    public function setEmail($email)
    {

        return $this->setData(self::EMAIL, $email);
    }

    /**
     * Get customer_id
     *
     * @return string
     */
    public function getCustomerId()
    {

        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * Set customer_id
     *
     * @param string $customer_id
     *
     * @return Records
     */
    public function setCustomerId($customer_id)
    {

        return $this->setData(self::CUSTOMER_ID, $customer_id);
    }

    /**
     * Get subscriber_id
     *
     * @return string
     */
    public function getSubscriberId()
    {

        return $this->getData(self::SUBSCRIBER_ID);
    }

    /**
     * Set subscriber_id
     *
     * @param string $subscriber_id
     *
     * @return Records
     */
    public function setSubscriberId($subscriber_id)
    {

        return $this->setData(self::SUBSCRIBER_ID, $subscriber_id);
    }

    /**
     * Get data_1
     *
     * @return string
     */
    public function getData1()
    {

        return $this->getData(self::DATA_1);
    }

    /**
     * Set data_1
     *
     * @param string $data_1
     *
     * @return Records
     */
    public function setData1($data_1)
    {

        return $this->setData(self::DATA_1, $data_1);
    }

    /**
     * Get data_2
     *
     * @return string
     */
    public function getData2()
    {

        return $this->getData(self::DATA_2);
    }

    /**
     * Set data_2
     *
     * @param string $data_2
     *
     * @return Records
     */
    public function setData2($data_2)
    {

        return $this->setData(self::DATA_2, $data_2);
    }

    /**
     * Get data_3
     *
     * @return string
     */
    public function getData3()
    {

        return $this->getData(self::DATA_3);
    }

    /**
     * Set data_3
     *
     * @param string $data_3
     *
     * @return Records
     */
    public function setData3($data_3)
    {

        return $this->setData(self::DATA_3, $data_3);
    }

    /**
     * Get data_4
     *
     * @return string
     */
    public function getData4()
    {

        return $this->getData(self::DATA_4);
    }

    /**
     * Set data_4
     *
     * @param string $data_4
     *
     * @return Records
     */
    public function setData4($data_4)
    {

        return $this->setData(self::DATA_4, $data_4);
    }

    /**
     * Get data_5
     *
     * @return string
     */
    public function getData5()
    {

        return $this->getData(self::DATA_5);
    }

    /**
     * Set data_5
     *
     * @param string $data_5
     *
     * @return Records
     */
    public function setData5($data_5)
    {

        return $this->setData(self::DATA_5, $data_5);
    }

    /**
     * Get data_6
     *
     * @return string
     */
    public function getData6()
    {

        return $this->getData(self::DATA_6);
    }

    /**
     * Set data_6
     *
     * @param string $data_6
     *
     * @return Records
     */
    public function setData6($data_6)
    {

        return $this->setData(self::DATA_6, $data_6);
    }

    /**
     * Get data_7
     *
     * @return string
     */
    public function getData7()
    {

        return $this->getData(self::DATA_7);
    }

    /**
     * Set data_7
     *
     * @param string $data_7
     *
     * @return Records
     */
    public function setData7($data_7)
    {

        return $this->setData(self::DATA_7, $data_7);
    }

    /**
     * Get data_8
     *
     * @return string
     */
    public function getData8()
    {

        return $this->getData(self::DATA_8);
    }

    /**
     * Set data_8
     *
     * @param string $data_8
     *
     * @return Records
     */
    public function setData8($data_8)
    {

        return $this->setData(self::DATA_8, $data_8);
    }

    /**
     * Get data_9
     *
     * @return string
     */
    public function getData9()
    {

        return $this->getData(self::DATA_9);
    }

    /**
     * Set data_9
     *
     * @param string $data_9
     *
     * @return Records
     */
    public function setData9($data_9)
    {

        return $this->setData(self::DATA_9, $data_9);
    }

    /**
     * Get data_10
     *
     * @return string
     */
    public function getData10()
    {

        return $this->getData(self::DATA_10);
    }

    /**
     * Set data_10
     *
     * @param string $data_10
     *
     * @return Records
     */
    public function setData10($data_10)
    {

        return $this->setData(self::DATA_10, $data_10);
    }
}
