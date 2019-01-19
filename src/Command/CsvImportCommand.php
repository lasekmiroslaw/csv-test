<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CsvImportCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
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
        $io->title('Attempting import of Products...');

        $reader = Reader::createFromPath($path, 'r')
            ->setHeaderOffset(0)
            ->setDelimiter(';');
        $header = $reader->getHeader();
        $records = $reader->getRecords();

        $recordsCount = iterator_count($records);
        $io->progressStart($recordsCount);

        foreach ($records as $offset => $record) {
            $product = new Product();
            $product->setMpn((string) $record['mpn']);
            $product->setQty((int) $record['qty']);
            $product->setManufactureYear((int) substr($record['description'], -4));
            $product->setPrice((float) $record['price']);

            $this->em->persist($product);
            $io->progressAdvance();
        }

        $this->em->flush();
        $io->progressFinish();
        $io->success('Records imported!');
    }
}
