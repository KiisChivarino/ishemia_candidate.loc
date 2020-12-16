<?php

namespace App\Repository;

use App\Entity\PatientSMS;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PatientSMS|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientSMS|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientSMS[]    findAll()
 * @method PatientSMS[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientSMSRepository extends AppRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientSMS::class);
    }
}