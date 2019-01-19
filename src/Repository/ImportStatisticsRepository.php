<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ImportStatistics;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method (ImportStatistics | null) find($id, $lockMode = null, $lockVersion=null)
 * @method (ImportStatistics | null) findOneBy(array $criteria, array $orderBy=null)
 * @method ImportStatistics[]    findAll()
 * @method ImportStatistics[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImportStatisticsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ImportStatistics::class);
    }
}
