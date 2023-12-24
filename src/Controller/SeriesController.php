<?php

namespace App\Controller;

use App\Entity\Series;
use App\Repository\SeriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SeriesController extends AbstractController
{
    public function __construct(
        private readonly SeriesRepository $seriesRepository
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
        return $this->render('series/form.html.twig');
    }

    #[Route('/series/create',  name: 'app_series_add', methods: ['POST'])]
    public function addSeries(Request $request): Response
    {
        $seriesName = $request->request->get('name');
        $newSeries = new Series($seriesName);
        $this->seriesRepository->add($newSeries, true);

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
        $serie = $this->seriesRepository->find($id);
        return $this->render('series/form.edit.html.twig', compact('serie'));
    }

    #[Route('/series/edit/{seriesId}', name: 'app_series_edit', methods: ['PUT'])]
    public function editSeries(int $seriesId, Request $request): Response
    {
        $series = $this->seriesRepository->find($seriesId);
        $series->changeName($request->request->get('name'));
        $this->seriesRepository->add($series, true);
        $this->addFlash('success', 'Serie editada com sucesso.');

        return $this->redirect('/series');
    }
}
