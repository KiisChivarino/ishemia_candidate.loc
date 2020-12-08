<?php

namespace App\Repository;

use App\Entity\StartingPoint;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StartingPoint|null find($id, $lockMode = null, $lockVersion = null)
 * @method StartingPoint|null findOneBy(array $criteria, array $orderBy = null)
 * @method StartingPoint[]    findAll()
 * @method StartingPoint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StartingPointRepository extends AppRepository
{
    /**
     * StartingPointRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StartingPoint::class);
    }

    /**
     * Add starting point from fixtures
     * @param int $id
     * @param string $name
     * @param string $title
     * @return StartingPoint
     * @throws ORMException
     */
    public function addStartingPointFromFixtures(int $id, string $name, string $title): StartingPoint
    {
        $newStartingPoint = (new StartingPoint())->setId($id)->setName($name)->setTitle($title);
        $this->_em->persist($newStartingPoint);
        return $newStartingPoint;
    }
}
