<?php

namespace App\Repository;

use App\Entity\District;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class DistrictRepository
 * @method District|null find($id, $lockMode = null, $lockVersion = null)
 * @method District|null findOneBy(array $criteria, array $orderBy = null)
 * @method District[]    findAll()
 * @method District[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class DistrictRepository extends AppRepository
{
    /**
     * DistrictRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, District::class);
    }
}
