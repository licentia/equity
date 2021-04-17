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

namespace Licentia\Equity\Console\Command;

use Licentia\Reports\Model\Indexer;
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
     * @var \Licentia\Reports\Model\IndexerFactory
     */
    protected $indexer;

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
     * @param \Licentia\Reports\Model\IndexerFactory $indexer
     * @param \Licentia\Equity\Helper\Data           $pandaHelper
     * @param \Licentia\Equity\Model\SegmentsFactory $pandaFactory
     */
    public function __construct(
        \Licentia\Reports\Model\IndexerFactory $indexer,
        \Licentia\Equity\Helper\Data $pandaHelper,
        \Licentia\Equity\Model\SegmentsFactory $pandaFactory
    ) {

        parent::__construct();
        $this->indexer = $indexer->create();
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

        if (!$this->indexer->canReindex('segments')) {
            throw new \RuntimeException("Indexer status does not allow reindexing");
        }

        $this->indexer->updateIndexStatus(Indexer::STATUS_WORKING, 'segments');

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
            $segmentId = 0;
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

        $this->indexer->updateIndexStatus(Indexer::STATUS_VALID, 'segments');

        $output->writeln("Segments | FINISHED: " . $this->pandaHelper->gmtDate());
        if ($customerId) {
            $output->writeln("Segments | Built only for customer ID " . $customerId);
        }
        $output->writeln("Segments | The process took " . $diff->format('%h Hours %i Minutes and %s Seconds'));
        $output->writeln("Segments | ");
    }
}
