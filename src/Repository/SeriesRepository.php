<?php

namespace App\Repository;

use App\DTO\SeriesCreateInputDTO;
use App\Entity\Series;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * @extends ServiceEntityRepository<Series>
 *
 * @method Series|null find($id, $lockMode = null, $lockVersion = null)
 * @method Series|null findOneBy(array $criteria, array $orderBy = null)
 * @method Series[]    findAll()
 * @method Series[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeriesRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private SeasonRepository $seasonRepository,
        private EpisodeRepository $episodeRepository,
        private LoggerInterface $logger
    ) {
        parent::__construct($registry, Series::class);
    }

    public function all()
    {
        return $this->createQueryBuilder('serie')
            ->orderBy('serie.id','DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param SeriesCreateInputDTO $input
     * @param bool $flush
     * @return Series
     */
    public function add(SeriesCreateInputDTO $input, bool $flush = false)
    {
        $entityManager = $this->getEntityManager();

        $series = new Series($input->seriesName, $input->coverImage);
        $entityManager->persist($series);
        $entityManager->flush();

        try {
            $this->seasonRepository->addSeasonsQuantity($input->seasonsQuantity, $series->getId());
            $seasons = $this->seasonRepository->findBy(['series' => $series]);
            $this->episodeRepository->addEpisodesPerSeason($input->episodePerSeason, $seasons);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->remove($series, true);
        }

        return $series;
    }

    public function remove(Series $series, bool $flush = false): void
    {
        $this->getEntityManager()->remove($series);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function deleteById(int $id): void
    {
        $series = $this->getEntityManager()->getReference(Series::class, $id);
        $this->remove($series, true);
    }
}
