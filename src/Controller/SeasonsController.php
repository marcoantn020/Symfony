<?php

namespace App\Controller;

use App\Repository\SeriesRepository;
use Psr\Cache\InvalidArgumentException;
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
        $seasons = $series->getSeasons();

        return $this->render('seasons/index.html.twig', [
            'series' => $series,
            'seasons' => $seasons
        ]);
    }
}
