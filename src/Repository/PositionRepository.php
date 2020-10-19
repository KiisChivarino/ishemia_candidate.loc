<?php

namespace App\Repository;

use App\Entity\Position;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Position|null find($id, $lockMode = null, $lockVersion = null)
 * @method Position|null findOneBy(array $criteria, array $orderBy = null)
 * @method Position[]    findAll()
 * @method Position[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PositionRepository extends AppRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Position::class);
    }
}
