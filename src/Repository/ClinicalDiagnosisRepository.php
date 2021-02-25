<?php

namespace App\Repository;

use App\Entity\ClinicalDiagnosis;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class ClinicalDiagnosisRepository
 * @method ClinicalDiagnosis|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClinicalDiagnosis|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClinicalDiagnosis[]    findAll()
 * @method ClinicalDiagnosis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class ClinicalDiagnosisRepository extends AppRepository
{
    /**
     * ClinicalDiagnosisRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClinicalDiagnosis::class);
    }
}
