<?php

namespace App\Tests\Repository;

use App\DTO\SeriesCreateInputDTO;
use App\Repository\EpisodeRepository;
use App\Repository\SeriesRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SeriesRepositoryTest extends KernelTestCase
{
    public function testSomething(): void
    {
        $kernel = self::bootKernel();

        $container = static::getContainer();
        $seriesRepository = $container->get(SeriesRepository::class);

        $seriesDTO = new SeriesCreateInputDTO(
            seriesName: 'The mentalist',
            seasonsQuantity: 2,
            episodePerSeason: 5
        );

         $seriesRepository->add($seriesDTO);

         $episodeRepository = $container->get(EpisodeRepository::class);
         $episodes = $episodeRepository->findAll();

         self::assertCount(10, $episodes);
    }
}
