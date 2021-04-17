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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Rebuild
 *
 * @package Licentia\Panda\Console\Command
 */
class Equity extends Command
{

    const CUSTOMER_ARGUMENT = 'customer';

    /**
     * @var \Licentia\Equity\Model\MetadataFactory
     */
    protected $metadataFactory;

    /**
     * @var \Licentia\Equity\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * Equity constructor.
     *
     * @param \Licentia\Equity\Helper\Data           $pandaHelper
     * @param \Magento\Framework\App\State           $appState
     * @param \Licentia\Equity\Model\MetadataFactory $pandaFactory
     */
    public function __construct(
        \Licentia\Equity\Helper\Data $pandaHelper,
        \Magento\Framework\App\State $appState,
        \Licentia\Equity\Model\MetadataFactory $pandaFactory
    ) {

        parent::__construct();

        $this->appState = $appState;

        $this->pandaHelper = $pandaHelper;
        $this->metadataFactory = $pandaFactory;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {

        $this->setName('panda:equity:rebuild')
             ->setDescription('Rebuilds customers equity')
             ->setDefinition(
                 [
                     new InputArgument(
                         self::CUSTOMER_ARGUMENT,
                         InputArgument::OPTIONAL,
                         'Rebuild only the specified customer ID'
                     ),
                 ]
             );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        try {
            $this->appState->setAreaCode('frontend');
        } catch (\Exception $e) {
        }

        $start = date_create($this->pandaHelper->gmtDate());
        $output->writeln("Equity |");
        $output->writeln("Equity | STARTED: " . $this->pandaHelper->gmtDate());

        try {
            $customerId = $input->getArgument(self::CUSTOMER_ARGUMENT);
        } catch (\Exception $e) {
            $customerId = null;
        }

        if (!is_numeric($customerId) || (int) $customerId == 0) {
            $customerId = null;
        }

        $i = 0;

        try {
            $this->metadataFactory->create()->rebuildCustomerMetadataCommandLine($output, $customerId, true);

            $i++;
        } catch (\Exception $e) {
            $output->writeln("<error>Equity | " . $e->getMessage() . '</error>');
        }

        if ($i > 1000) {
            return;
        }

        $end = date_create($this->pandaHelper->gmtDate());
        $diff = date_diff($end, $start);

        $output->writeln("Equity | FINISHED: " . $this->pandaHelper->gmtDate());
        if ($customerId) {
            $output->writeln("Equity | Built only for customer ID " . $customerId);
        }
        $output->writeln("Equity | The process took " . $diff->format('%h Hours %i Minutes and %s Seconds'));
        $output->writeln("Equity | ");
    }
}
