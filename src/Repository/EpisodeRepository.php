<?php

namespace App\Repository;

use App\Entity\Episode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Episode>
 *
 * @method Episode|null find($id, $lockMode = null, $lockVersion = null)
 * @method Episode|null findOneBy(array $criteria, array $orderBy = null)
 * @method Episode[]    findAll()
 * @method Episode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EpisodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Episode::class);
    }

    public function setEpisodeWatched(array $episodes, bool $watched): void
    {
        $watched = $watched ? 1 : 0;
        $queryBuilder = $this->createQueryBuilder('episode');

        $queryBuilder->update('App\Entity\Episode', 'episode')
            ->set('episode.watched', $watched)
            ->where($queryBuilder->expr()->in('episode.id', $episodes));

        $query = $queryBuilder->getQuery();
        $query->execute();
    }
}
