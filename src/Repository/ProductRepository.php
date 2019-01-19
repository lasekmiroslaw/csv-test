<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method (Product | null) find($id, $lockMode = null, $lockVersion=null)
 * @method (Product | null) findOneBy(array $criteria, array $orderBy=null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findAllProducts(int $page = 1, int $limit = 25): Paginator
    {
        $qb = $this->createQueryBuilder('p');

        $paginator = $this->paginate($qb, $page, $limit);

        return  $paginator;
    }

    public function paginate(QueryBuilder $qb, int $page, int $limit): Paginator
    {
        $paginator = new Paginator($qb, $page);
        $offset = $limit * ($page - 1);

        $paginator->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $paginator;
    }

    public function findByMpns($mpns): array
    {
        $mpns = $this->explodeMpns($mpns);

        return $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.mpn IN(:mpns)')
            ->setParameter('mpns', $mpns)
            ->getQuery()
            ->getResult()
            ;
    }

    private function explodeMpns($mpns): array
    {
        return array_map('trim', explode(',', $mpns));
    }
}
