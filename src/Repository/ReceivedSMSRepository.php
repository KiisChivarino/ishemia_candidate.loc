<?php

namespace App\Repository;

use App\Entity\ReceivedSMS;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReceivedSMS|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReceivedSMS|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReceivedSMS[]    findAll()
 * @method ReceivedSMS[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReceivedSMSRepository extends AppRepository
{
    /**
     * ReceivedSMSRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReceivedSMS::class);
    }
}
