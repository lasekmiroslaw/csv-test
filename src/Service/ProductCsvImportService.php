<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ImportStatistics;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use League\Csv\Writer;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ProductCsvImportService
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
        $this->em = $em;
        $this->validator = $validator;
    }

    public function importFromPath(string $path, SymfonyStyle $io): void
    {
        if (!$this->isCsvFile($path)) {
            throw new InvalidOptionException("Provided csv file path '${path}' is not valid");
        }
        $io->title('Attempting import of Products...');

        $reader = $this->createReader($path);
        $records = $reader->getRecords();

        $writer = $this->createWriter();
        $header = $reader->getHeader();
        $writer->insertOne($header);

        $invalidRecords = [];
        $recordsCount = iterator_count($records);
        $io->progressStart($recordsCount);

        foreach ($records as $offset => $record) {
            $product = $this->createProductFromRecord($record);
            $errors = $this->validator->validate($product);

            if (count($errors) > 0) {
                $invalidRecords[] = $record;
            } else {
                $this->em->persist($product);
            }

            $io->progressAdvance();
        }
        $notImportedRecordsCount = count($invalidRecords);
        $importedRecordsCount = ($recordsCount - $notImportedRecordsCount);
        $importStatistics = new ImportStatistics($recordsCount, $importedRecordsCount);

        $this->em->persist($importStatistics);
        $this->em->flush();
        $writer->insertAll($invalidRecords);

        $io->progressFinish();
        $io->success("${importedRecordsCount} from ${recordsCount} records were imported!");
    }

    private function isCsvFile(string $path): bool
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

    private function createReader(string $path): Reader
    {
        return Reader::createFromPath($path, 'r')
            ->setHeaderOffset(0)
            ->setDelimiter(';');
    }

    private function createWriter(): Writer
    {
        return Writer::createFromPath('var/data/' . uniqid(date('d-m-Y_'), true) . '.csv', 'w+');
    }

    private function createProductFromRecord(array $record): Product
    {
        $product = new Product();
        $product->setMpn((string) $record['mpn']);
        $product->setQty((int) $record['qty']);
        $product->setManufactureYear((int) substr($record['description'], -4));
        $product->setPrice((float) $record['price']);

        return $product;
    }
}
