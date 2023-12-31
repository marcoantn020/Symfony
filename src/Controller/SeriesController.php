<?php

namespace App\Controller;

use App\DTO\SeriesCreateFormInput;
use App\Entity\Episode;
use App\Entity\Season;
use App\Entity\Series;
use App\Form\SeriesFormType;
use App\Message\SeriesWasCreated;
use App\Repository\SeriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class SeriesController extends AbstractController
{
    public function __construct(
        private readonly SeriesRepository $seriesRepository,
        private MessageBusInterface $messageBus
    ) {
    }

    #[Route('/series', name: 'app_series', methods: ['GET'])]
    public function index(): Response
    {
        $seriesList = $this->seriesRepository->all();

        return $this->render(
            'series/index.html.twig',
            compact(
                'seriesList'
            ));
    }

    #[Route('/series/create', name: 'app_series_form', methods: ['GET'])]
    public function addSeriesForm(): Response
    {
        $seriesForm = $this->createForm(SeriesFormType::class, new SeriesCreateFormInput());
        return $this->renderForm('series/form.html.twig', compact('seriesForm'));
    }

    #[Route('/series/create',  name: 'app_series_add', methods: ['POST'])]
    public function addSeries(Request $request): Response
    {
        $input = new SeriesCreateFormInput();
        $seriesForm = $this->createForm(SeriesFormType::class, $input)
            ->handleRequest($request);

        if (!$seriesForm->isValid()) {
            return $this->renderForm('series/form.html.twig', compact('seriesForm'));
        }

        $series = new Series($input->seriesName);
        for ($i = 1; $i <= $input->seasonsQuantity; $i++) {
            $seasons = new Season($i);
            for ($j = 1; $j <= $input->episodePerSeason; $j++) {
                $episode = new Episode($j);
                $episode->setWatched(false);
                $seasons->addEpisode($episode);
            }
            $series->addSeason($seasons);
        }

        $this->seriesRepository->add($series, true);
        $this->messageBus->dispatch(new SeriesWasCreated($series));

        $this->addFlash('success', 'Serie adicionada com sucesso.');

        return $this->redirect('/series');
    }

    #[Route('/series/delete/{seriesId}',  name: 'app_series_delete', methods: ['DELETE'])]
    public function deleteSeries(int $seriesId): Response
    {
        $this->seriesRepository->deleteById(id: $seriesId);
        $this->addFlash('success', 'Serie excluida com sucesso.');
        return $this->redirect('/series');
    }

    #[Route('/series/edit/{id}', name: 'app_series_edit_form', methods: ['GET'])]
    public function editSeriesForm(int $id): Response
    {
        $series = $this->seriesRepository->find($id);
        $seriesForm = $this->createForm(SeriesFormType::class, $series, ['is_edit' => true]);
        return $this->renderForm('series/form.edit.html.twig', compact('seriesForm', 'series'));
    }

    #[Route('/series/edit/{seriesId}', name: 'app_series_edit', methods: ['PUT'])]
    public function editSeries(int $seriesId, Request $request): Response
    {
        $series = $this->seriesRepository->find($seriesId);
        $seriesForm = $this->createForm(SeriesFormType::class, $series, ['is_edit' => true])
            ->handleRequest($request);

        if (!$seriesForm->isValid()) {
            return $this->renderForm('series/form.edit.html.twig', compact('seriesForm','series'));
        }

        $this->seriesRepository->add($series, true);
        $this->addFlash('success', 'Serie editada com sucesso.');

        return $this->redirect('/series');
    }
}
