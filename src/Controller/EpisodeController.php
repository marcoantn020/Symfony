<?php

namespace App\Controller;

use App\Repository\EpisodeRepository;
use App\Repository\SeasonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EpisodeController extends AbstractController
{
    public function __construct(
        private readonly SeasonRepository  $seasonRepository,
        private readonly EpisodeRepository $episodeRepository
    )
    {
    }

    #[Route('/season/{seasonId}/episodes', name: 'app_episode', methods: ['GET'])]
    public function index(int $seasonId): Response
    {
        $season = $this->seasonRepository->find($seasonId);
        $episodes = $season->getEpisodes();
        $serie = $season->getSeries();

        return $this->render('episode/index.html.twig',
            compact(
                'episodes',
                'season',
                'serie'
            )
        );
    }

    #[Route('/season/{seasonId}/episodes', name: 'app_episode_watched_episode', methods: ['POST'])]
    public function watchedEpisode(int $seasonId, Request $request)
    {
        $season = $this->seasonRepository->find($seasonId);
        $watchedEpisodes = array_keys($request->request->all('episodes'));

        if (empty($watchedEpisodes)) {
            $watchedEpisodes = array_map(
                fn($episode) => $episode->getId(),
                $season->getEpisodes()->toArray()
            );
            $this->episodeRepository->setEpisodeWatched($watchedEpisodes, false);
            $this->addFlash('success', 'Episodios desmarcados como assistidos');
            return $this->redirect("/season/{$season->getId()}/episodes");
        }

        $this->episodeRepository->setEpisodeWatched($watchedEpisodes, true);
        $this->addFlash('success', 'Episodios marcados como assistidos');
        return $this->redirect("/season/{$season->getId()}/episodes");
    }
}
