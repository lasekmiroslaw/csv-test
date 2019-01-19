<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ImportStatisticsRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RecordsController extends AbstractController
{
    public const RECORDS_PER_PAGE = 100;

    /**
     * @var ImportStatisticsRepository
     */
    private $statisticsRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(ImportStatisticsRepository $statisticsRepository, ProductRepository $productRepository)
    {
        $this->statisticsRepository = $statisticsRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/statistics", name="statistics")
     */
    public function statistics()
    {
        $lastImported = $this->statisticsRepository->findLastImported();

        return $this->render('records/statistics.html.twig', [
            'lastImported' => $lastImported,
        ]);
    }

    /**
     * @Route("/records", name="records")
     */
    public function records(Request $request)
    {
        $page = $request->query->get('page', 1);
        $limit = self::RECORDS_PER_PAGE;
        /**
         * @var Paginator
         */
        $products = $this->productRepository->findAllProducts((int) $page, $limit);
        $lastPage = ceil($products->count() / $limit);

        return $this->render('records/records.html.twig', [
            'products' => $products,
            'page' => $page,
            'lastPage' => $lastPage,
        ]);
    }
}
