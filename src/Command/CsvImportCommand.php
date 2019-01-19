<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ProductCsvImportService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class CsvImportCommand extends Command
{
    /**
     * @var ProductCsvImportService
     */
    private $productCsvImport;

    public function __construct(ProductCsvImportService $productCsvImport)
    {
        parent::__construct();

        $this->productCsvImport = $productCsvImport;
    }

    protected function configure()
    {
        $this
            ->setName('csv:import')
            ->setDescription('Import products from CSV file')
            ->setHelp('This command allows you import products from csv file to database')
            ->addOption('path', null, InputOption::VALUE_REQUIRED, 'csv file path', '');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getOption('path');

        $io = new SymfonyStyle($input, $output);
        $this->productCsvImport->importFromPath($path, $io);
    }
}
