<?php

namespace App\Command;

use App\BusinessProcess\RateBusinessProcess;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CurrencyParserCommand extends Command
{
    protected static $defaultName = 'crassula:currency:parser';

    private RateBusinessProcess $rateBusinessProcess;

    public function __construct(RateBusinessProcess $rateBusinessProcess)
    {
        parent::__construct(null);

        $this->rateBusinessProcess = $rateBusinessProcess;
    }

    protected function configure()
    {
        $this->setDescription('Currency parser command.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->rateBusinessProcess->emitRates();
        } catch (\Throwable $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        $io->success('Success.');

        return Command::SUCCESS;
    }
}
