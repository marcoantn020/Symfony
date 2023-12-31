<?php

namespace App\Repository;

use App\Entity\Episode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
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

    /**
     * @param int $episodesPerSeason
     * @param array $seasons
     * @return void
     * @throws Exception
     */
    public function addEpisodesPerSeason(int $episodesPerSeason, array $seasons): void
    {
        $params = array_fill(0, $episodesPerSeason, '(?, ?)');
        $connection = $this->getEntityManager()->getConnection();
        $sql = 'INSERT INTO episode (season_id, number) VALUES ' . implode(', ', $params);
        $stm = $connection->prepare($sql);

        foreach ($seasons as $season) {
            for ($i = 0; $i < $episodesPerSeason; $i++) {
                $stm->bindValue($i * 2 + 1, $season->getId(), \PDO::PARAM_INT);
                $stm->bindValue($i * 2 + 2, $i + 1, \PDO::PARAM_INT);
            }
            $stm->executeQuery();
        }
    }
}
