<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use League\Csv\Writer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CsvImportCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator)
    {
        parent::__construct();

        $this->em = $em;
        $this->validator = $validator;
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

        if (!$this->isCsvFile($path)) {
            throw new InvalidOptionException("Provided csv file path '${path}' is not valid");
        }

        $io = new SymfonyStyle($input, $output);
        $io->title('Attempting import of Products...');

        $reader = Reader::createFromPath($path, 'r')
            ->setHeaderOffset(0)
            ->setDelimiter(';');
        $header = $reader->getHeader();
        $records = $reader->getRecords();

        $writer = Writer::createFromPath('var/data/' . uniqid(date('d-m-Y_'), true) . '.csv', 'w+');
        $writer->insertOne($header);
        $invalidRecords = [];

        $recordsCount = iterator_count($records);
        $io->progressStart($recordsCount);

        foreach ($records as $offset => $record) {
            $product = new Product();
            $product->setMpn((string) $record['mpn']);
            $product->setQty((int) $record['qty']);
            $product->setManufactureYear((int) substr($record['description'], -4));
            $product->setPrice((float) $record['price']);

            $errors = $this->validator->validate($product);

            if (count($errors) > 0) {
                $invalidRecords[] = $record;
            } else {
                $this->em->persist($product);
            }

            $io->progressAdvance();
        }

        $this->em->flush();
        $writer->insertAll($invalidRecords);
        $io->progressFinish();
        $io->success('Records imported!');
    }

    private function isCsvFile(string $path)
    {
        $csvMimetypes = [
            'text/csv',
            'text/plain',
            'application/csv',
            'text/comma-separated-values',
            'application/excel',
            'application/vnd.ms-excel',
            'application/vnd.msexcel',
            'text/anytext',
            'application/octet-stream',
            'application/txt',
        ];

        if (is_file($path) && in_array(mime_content_type($path), $csvMimetypes)) {
            return true;
        }

        return false;
    }
}
