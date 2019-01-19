<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ImportStatisticsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RecordsController extends AbstractController
{
    /**
     * @var ImportStatisticsRepository
     */
    private $statisticsRepository;

    public function __construct(ImportStatisticsRepository $statisticsRepository)
    {
        $this->statisticsRepository = $statisticsRepository;
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
}
