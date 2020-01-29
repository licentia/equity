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

namespace Licentia\Equity\Api\Data;

/**
 * Interface RecordsInterface
 *
 * @package Licentia\Panda\Api\Data
 */
interface RecordsInterface
{

    const DATA_3 = 'data_3';

    const DATA_6 = 'data_6';

    const DATA_10 = 'data_10';

    const DATA_4 = 'data_4';

    const RECORD_ID = 'record_id';

    const FIRSTNAME = 'firstname';

    const DATA_1 = 'data_1';

    const DATA_7 = 'data_7';

    const DATA_9 = 'data_9';

    const CUSTOMER_ID = 'customer_id';

    const DATA_2 = 'data_2';

    const DATA_8 = 'data_8';

    const SEGMENT_ID = 'segment_id';

    const LASTNAME = 'lastname';

    const EMAIL = 'email';

    const SUBSCRIBER_ID = 'subscriber_id';

    const DATA_5 = 'data_5';

    /**
     * Get record_id
     *
     * @return string|null
     */

    public function getRecordId();

    /**
     * Set record_id
     *
     * @param string $record_id
     *
     * @return \Licentia\Equity\Api\Data\RecordsInterface
     */

    public function setRecordId($record_id);

    /**
     * Get segment_id
     *
     * @return string|null
     */

    public function getSegmentId();

    /**
     * Set segment_id
     *
     * @param string $segment_id
     *
     * @return \Licentia\Equity\Api\Data\RecordsInterface
     */

    public function setSegmentId($segment_id);

    /**
     * Get firstname
     *
     * @return string|null
     */

    public function getFirstname();

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return \Licentia\Equity\Api\Data\RecordsInterface
     */

    public function setFirstname($firstname);

    /**
     * Get lastname
     *
     * @return string|null
     */

    public function getLastname();

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return \Licentia\Equity\Api\Data\RecordsInterface
     */

    public function setLastname($lastname);

    /**
     * Get email
     *
     * @return string|null
     */

    public function getEmail();

    /**
     * Set email
     *
     * @param string $email
     *
     * @return \Licentia\Equity\Api\Data\RecordsInterface
     */

    public function setEmail($email);

    /**
     * Get customer_id
     *
     * @return string|null
     */

    public function getCustomerId();

    /**
     * Set customer_id
     *
     * @param string $customer_id
     *
     * @return \Licentia\Equity\Api\Data\RecordsInterface
     */

    public function setCustomerId($customer_id);

    /**
     * Get subscriber_id
     *
     * @return string|null
     */

    public function getSubscriberId();

    /**
     * Set subscriber_id
     *
     * @param string $subscriber_id
     *
     * @return \Licentia\Equity\Api\Data\RecordsInterface
     */

    public function setSubscriberId($subscriber_id);

    /**
     * Get data_1
     *
     * @return string|null
     */

    public function getData1();

    /**
     * Set data_1
     *
     * @param string $data_1
     *
     * @return \Licentia\Equity\Api\Data\RecordsInterface
     */

    public function setData1($data_1);

    /**
     * Get data_2
     *
     * @return string|null
     */

    public function getData2();

    /**
     * Set data_2
     *
     * @param string $data_2
     *
     * @return \Licentia\Equity\Api\Data\RecordsInterface
     */

    public function setData2($data_2);

    /**
     * Get data_3
     *
     * @return string|null
     */

    public function getData3();

    /**
     * Set data_3
     *
     * @param string $data_3
     *
     * @return \Licentia\Equity\Api\Data\RecordsInterface
     */

    public function setData3($data_3);

    /**
     * Get data_4
     *
     * @return string|null
     */

    public function getData4();

    /**
     * Set data_4
     *
     * @param string $data_4
     *
     * @return \Licentia\Equity\Api\Data\RecordsInterface
     */

    public function setData4($data_4);

    /**
     * Get data_5
     *
     * @return string|null
     */

    public function getData5();

    /**
     * Set data_5
     *
     * @param string $data_5
     *
     * @return \Licentia\Equity\Api\Data\RecordsInterface
     */

    public function setData5($data_5);

    /**
     * Get data_6
     *
     * @return string|null
     */

    public function getData6();

    /**
     * Set data_6
     *
     * @param string $data_6
     *
     * @return \Licentia\Equity\Api\Data\RecordsInterface
     */

    public function setData6($data_6);

    /**
     * Get data_7
     *
     * @return string|null
     */

    public function getData7();

    /**
     * Set data_7
     *
     * @param string $data_7
     *
     * @return \Licentia\Equity\Api\Data\RecordsInterface
     */

    public function setData7($data_7);

    /**
     * Get data_8
     *
     * @return string|null
     */

    public function getData8();

    /**
     * Set data_8
     *
     * @param string $data_8
     *
     * @return \Licentia\Equity\Api\Data\RecordsInterface
     */

    public function setData8($data_8);

    /**
     * Get data_9
     *
     * @return string|null
     */

    public function getData9();

    /**
     * Set data_9
     *
     * @param string $data_9
     *
     * @return \Licentia\Equity\Api\Data\RecordsInterface
     */

    public function setData9($data_9);

    /**
     * Get data_10
     *
     * @return string|null
     */

    public function getData10();

    /**
     * Set data_10
     *
     * @param string $data_10
     *
     * @return \Licentia\Equity\Api\Data\RecordsInterface
     */

    public function setData10($data_10);
}
