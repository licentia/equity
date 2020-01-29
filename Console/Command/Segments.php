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

namespace Licentia\Equity\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Rebuild
 *
 * @package Licentia\Panda\Console\Command
 */
class Segments extends Command
{

    const CUSTOMER_ARGUMENT = 'customer';

    /**
     * @var \Licentia\Equity\Model\SegmentsFactory
     */
    protected $segmentsFactory;

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected $pandaHelper;

    /**
     * Segments constructor.
     *
     * @param \Licentia\Equity\Helper\Data           $pandaHelper
     * @param \Licentia\Equity\Model\SegmentsFactory $pandaFactory
     */
    public function __construct(
        \Licentia\Equity\Helper\Data $pandaHelper,
        \Licentia\Equity\Model\SegmentsFactory $pandaFactory
    ) {

        parent::__construct();
        $this->pandaHelper = $pandaHelper;
        $this->segmentsFactory = $pandaFactory;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {

        $this->setName('panda:segments:rebuild')
             ->setDescription('Rebuilds Segments Records')
             ->setDefinition(
                 [
                     new InputArgument(
                         self::CUSTOMER_ARGUMENT,
                         InputArgument::OPTIONAL,
                         'Rebuild only the specified segment ID'
                     ),
                 ]
             );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $start = date_create($this->pandaHelper->gmtDate());
        $output->writeln("Segments | ");
        $output->writeln("Segments | STARTED: " . $this->pandaHelper->gmtDate());

        try {
            $customerId = $input->getArgument(self::CUSTOMER_ARGUMENT);
        } catch (\Exception $e) {
            $customerId = null;
        }

        if (!is_numeric($customerId) || (int) $customerId == 0) {
            $customerId = null;
        }

        $collection = $this->segmentsFactory->create()
                                            ->getCollection()->addFieldToFilter('is_active', 1);

        $connection = $collection->getResource()->getConnection();

        $totalCustomers = $connection->fetchOne(
            $connection->select()
                       ->from(
                           $collection->getResource()
                                      ->getTable('customer_entity'),
                           ['MAX(entity_id)']
                       )
        );

        /** @var \Licentia\Equity\Model\Segments $segment */
        foreach ($collection as $segment) {
            try {
                $segmentId = $segment->getId();
                $startSegment = date_create($this->pandaHelper->gmtDate());
                $segment->setData('consoleOutput', $output);
                $segment->setData('totalCustomers', $totalCustomers);

                $segment->updateSegmentRecords($customerId);

                $output->writeln('');

                if ($customerId) {
                    $output->writeln(
                        "Segments | Segment " . $segment->getName() . " built only for customer ID " . $customerId
                    );
                } else {
                    $output->writeln("Segments | Segment " . $segment->getName() . " built");
                }

                $endSegment = date_create($this->pandaHelper->gmtDate());
                $diffSegment = date_diff($endSegment, $startSegment);
                $output->writeln(
                    "Segments | The process took " . $diffSegment->format('%h Hours %i Minutes and %s Seconds')
                );
            } catch (\Exception $e) {
                $output->writeln("<error>Segments | Seg ID:" . $segmentId . ' -- ' . $e->getMessage() . '</error>');
            }
        }

        $end = date_create($this->pandaHelper->gmtDate());
        $diff = date_diff($end, $start);

        $output->writeln("Segments | FINISHED: " . $this->pandaHelper->gmtDate());
        if ($customerId) {
            $output->writeln("Segments | Built only for customer ID " . $customerId);
        }
        $output->writeln("Segments | The process took " . $diff->format('%h Hours %i Minutes and %s Seconds'));
        $output->writeln("Segments | ");
    }
}
