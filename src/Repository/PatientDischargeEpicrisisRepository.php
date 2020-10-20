<?php

namespace App\Repository;

use App\Entity\PatientDischargeEpicrisis;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PatientDischargeEpicrisisRepository
 * @method PatientDischargeEpicrisis|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientDischargeEpicrisis|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientDischargeEpicrisis[]    findAll()
 * @method PatientDischargeEpicrisis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @package App\Repository
 */
class PatientDischargeEpicrisisRepository extends AppRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientDischargeEpicrisis::class);
    }
}
