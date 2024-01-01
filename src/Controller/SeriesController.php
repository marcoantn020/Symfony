<?php

namespace App\Controller;

use App\DTO\SeriesCreateInputDTO;
use App\Form\SeriesFormType;
use App\Message\SeriesWasCreated;
use App\Message\SeriesWasRemoved;
use App\Repository\SeriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SeriesController extends AbstractController
{
    public function __construct(
        private readonly SeriesRepository $seriesRepository,
        private readonly MessageBusInterface $messageBus,
        private readonly SluggerInterface $slugger,
        private readonly TranslatorInterface $translator
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
        $seriesForm = $this->createForm(SeriesFormType::class, new SeriesCreateInputDTO());
        return $this->renderForm('series/form.html.twig', compact('seriesForm'));
    }

    #[Route('/series/create',  name: 'app_series_add', methods: ['POST'])]
    public function addSeries(Request $request): Response
    {
        $input = new SeriesCreateInputDTO();
        $seriesForm = $this->createForm(SeriesFormType::class, $input)
            ->handleRequest($request);

        if (!$seriesForm->isValid()) {
            return $this->renderForm('series/form.html.twig', compact('seriesForm'));
        }

        /** @var UploadedFile $uploadCoverImage */
        $uploadCoverImage = $seriesForm->get('coverImage')->getData();

        if ($uploadCoverImage) {
            $originalFilename = pathinfo(
                $uploadCoverImage->getClientOriginalName(),
                PATHINFO_FILENAME
            );

            $safeFilename = $this->slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadCoverImage->guessExtension();

            $uploadCoverImage->move(
                $this->getParameter('cover_image_director'),
                $newFilename
            );

            $input->coverImage = $newFilename;
        }

        $series = $this->seriesRepository->add($input);
        $this->messageBus->dispatch(new SeriesWasCreated($series));

        $this->addFlash('success', 'Serie adicionada com sucesso.');

        return $this->redirectToRoute('app_series');
    }

    #[Route('/series/delete/{seriesId}',  name: 'app_series_delete', methods: ['DELETE'])]
    public function deleteSeries(int $seriesId): Response
    {
        $series = $this->seriesRepository->find($seriesId);
        $this->seriesRepository->remove(series: $series, flush: true);
        $this->messageBus->dispatch(new SeriesWasRemoved($series));
        $this->addFlash('success', $this->translator->trans('series.delete'));
        return $this->redirectToRoute('app_series');
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

        return $this->redirectToRoute('app_series');
    }
}
