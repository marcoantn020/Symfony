<?php

namespace App\Controller;

use App\Repository\SeasonRepository;
use App\Repository\SeriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SeasonsController extends AbstractController
{
    public function __construct(
        private readonly SeriesRepository $seriesRepository
    ) {
    }

    #[Route('/series/{seriesId}/seasons', name: 'app_seasons')]
    public function index(int $seriesId): Response
    {
        $series = $this->seriesRepository->find($seriesId);

        return $this->render('seasons/index.html.twig', [
            'series' => $series,
            'seasons' => $series->getSeasons()
        ]);
    }
}
