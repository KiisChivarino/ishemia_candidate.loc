<?php

namespace App\Repository;

use App\Entity\Patient;
use App\Entity\RiskFactor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\PersistentCollection;

/**
 * @method Patient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Patient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Patient[]    findAll()
 * @method Patient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientRepository extends ServiceEntityRepository
{
    /**
     * PatientRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Patient::class);
    }

    /**
     * Получить сумму баллов факторов риска
     *
     * @param Patient $patient
     *
     * @return int|null
     */
    public function getTotalRiskFactorScores(Patient $patient)
    {
        $totalScores = 0;
        /** @var PersistentCollection $riskFactors */
        $riskFactors = $patient->getRiskFactor();
        /** @var RiskFactor $factor */
        foreach ($riskFactors as $factor) {
            $totalScores += $factor->getScores();
        }
        return $totalScores;
    }
}
