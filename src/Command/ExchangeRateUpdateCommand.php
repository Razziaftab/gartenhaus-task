<?php

namespace App\Command;

use App\Service\CurrencyExchangeService;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:update-currency_exchange-rate', description: 'Update currency exchange rate in the database')]
class ExchangeRateUpdateCommand extends Command
{
    public function __construct(private CurrencyExchangeService $currencyExchangeService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This command update the currency currency exchange in the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Update Currency Exchange Rate',
            '============',
            '',
        ]);
        $this->currencyExchangeService->exchangeAPI();

        $output->writeln('Data Successfully Updated!');

        return Command::SUCCESS;
    }
}