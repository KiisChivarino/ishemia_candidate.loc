<?php

namespace App\Repository;

use App\Entity\MedicalHistory;
use App\Entity\PatientTesting;
use App\Services\InfoService\MedicalHistoryInfoService;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * Class PatientTestingRepository
 * @method PatientTesting|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientTesting|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientTesting[]    findAll()
 * @method PatientTesting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @package App\Repository
 */
class PatientTestingRepository extends AppRepository
{
    /** @var MedicalHistoryInfoService $medicalHistoryInfoService */
    private $medicalHistoryInfoService;

    /**
     * PatientTestingRepository constructor.
     *
     * @param ManagerRegistry $registry
     * @param MedicalHistoryInfoService $medicalHistoryInfoService
     */
    public function __construct(ManagerRegistry $registry, MedicalHistoryInfoService $medicalHistoryInfoService)
    {
        parent::__construct($registry, PatientTesting::class);
        $this->medicalHistoryInfoService = $medicalHistoryInfoService;
    }

    /**
     * Gets overdue testings
     * @param $patientId
     * @return int|mixed|string
     */
    public function getOverdueTestingsMenu($patientId)
    {
        return sizeof($this->createQueryBuilder('pT')
            ->leftJoin('pT.medicalHistory', 'mH')
            ->leftJoin('mH.patient', 'p')
            ->leftJoin('p.AuthUser', 'u')
            ->leftJoin('pT.analysisGroup', 'aG')
            ->leftJoin('pT.prescriptionTesting', 'prT')
            ->andWhere('prT.plannedDate < :dateTimeNow')
            ->andWhere('u.enabled = :val')
            ->andWhere('p.id = :patientId')
            ->andWhere('pT.hasResult = false')
            ->setParameter('patientId', $patientId)
            ->setParameter('dateTimeNow', new \DateTime('now'))
            ->setParameter('val', true)
            ->select('pT.id')
            ->distinct()
            ->getQuery()
            ->getResult());
    }

    /**
     * Gets planned testings
     * @param $patientId
     * @return int|mixed|string
     */
    public function getPlannedTestingsMenu($patientId)
    {
        return sizeof($this->createQueryBuilder('pT')
            ->leftJoin('pT.medicalHistory', 'mH')
            ->leftJoin('mH.patient', 'p')
            ->leftJoin('p.AuthUser', 'u')
            ->leftJoin('pT.analysisGroup', 'aG')
            ->leftJoin('pT.prescriptionTesting', 'prT')
            ->andWhere('prT.plannedDate >= :dateTimeNow')
            ->andWhere('u.enabled = :val')
            ->andWhere('p.id = :patientId')
            ->andWhere('pT.hasResult = false')
            ->setParameter('patientId', $patientId)
            ->setParameter('dateTimeNow', new \DateTime('now'))
            ->setParameter('val', true)
            ->select('pT.id')
            ->distinct()
            ->getQuery()
            ->getResult());
    }

    /**
     * Gets no processed testings
     * @param $patientId
     * @return int|mixed|string
     */
    public function getNoProcessedTestingsMenu($patientId)
    {
        return sizeof($this->createQueryBuilder('pT')
            ->leftJoin('pT.medicalHistory', 'mH')
            ->leftJoin('mH.patient', 'p')
            ->leftJoin('p.AuthUser', 'u')
            ->leftJoin('pT.analysisGroup', 'aG')
            ->andWhere('u.enabled = :val')
            ->andWhere('p.id = :patientId')
            ->andWhere('pT.processed = false')
            ->andWhere('pT.hasResult = true')
            ->setParameter('patientId', $patientId)
            ->setParameter('val', true)
            ->select('pT.id')
            ->distinct()
            ->getQuery()
            ->getResult());
    }

    /**
     * Get first testings
     * @param MedicalHistory $medicalHistory
     * @return int|mixed|string
     * @throws Exception
     */
    public function getFirstTestings(MedicalHistory $medicalHistory)
    {
        return $this->createQueryBuilder('pt')
            ->leftJoin('pt.medicalHistory', 'mh')
            ->where('pt.enabled= true')
            ->andWhere('pt.medicalHistory = :medicalHistory')
            ->andWhere('pt.isFirst = :isFirst')
            ->setParameter('medicalHistory', $medicalHistory)
            ->setParameter('isFirst', true)
            ->getQuery()
            ->getResult();
    }
}
