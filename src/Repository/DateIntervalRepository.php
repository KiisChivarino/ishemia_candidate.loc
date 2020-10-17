<?php

namespace App\Repository;

use App\Entity\DateInterval;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DateInterval|null find($id, $lockMode = null, $lockVersion = null)
 * @method DateInterval|null findOneBy(array $criteria, array $orderBy = null)
 * @method DateInterval[]    findAll()
 * @method DateInterval[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DateIntervalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DateInterval::class);
    }
}
