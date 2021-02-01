<?php

namespace App\Repository;

use App\Entity\MedicalHistory;
use App\Entity\PatientTesting;
use App\Services\InfoService\MedicalHistoryInfoService;
use DateTime;
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
     * Gets Patient Testings if PatientTesting has result of testing but is not processed by staff
     * @param $patient
     * @return int|mixed|string
     */
    public function getNoProcessedTestingsMenu()
    {
        return sizeof($this->createQueryBuilder('paT')
            ->leftJoin('paT.medicalHistory', 'mH')
            ->leftJoin('mH.patient', 'p')
            ->leftJoin('paT.prescriptionTesting', 'prT')
            ->leftJoin('p.AuthUser', 'u')
            ->andWhere('mH.enabled = true')
            ->andWhere('mH.dateEnd IS NULL')
            ->andWhere('u.enabled = true')
            ->andWhere('paT.hasResult = true')
            ->andWhere('paT.isProcessedByStaff = false')
            ->select('p.id')
            ->distinct()
            ->getQuery()
            ->getResult());
    }

    /**
     * Gets Patient Testings if PatientTesting has no results
     * @return int|mixed|string
     */
    public function getNoResultsTestingsMenu()
    {
        return sizeof($this->createQueryBuilder('paT')
            ->leftJoin('paT.medicalHistory', 'mH')
            ->leftJoin('mH.patient', 'p')
            ->leftJoin('paT.prescriptionTesting', 'prT')
            ->leftJoin('p.AuthUser', 'u')
            ->andWhere('mH.enabled = true')
            ->andWhere('mH.dateEnd IS NULL')
            ->andWhere('u.enabled = true')
            ->andWhere('paT.hasResult = false')
            ->andWhere('prT.plannedDate <= :dateTimeNow')
            ->setParameter('dateTimeNow', new DateTime('now'))
            ->select('p.id')
            ->distinct()
            ->getQuery()
            ->getResult());
    }

    /**
     * Gets Patient Testings if PatientTesting has result of testing but is not processed by staff
     * @param $patient
     * @return int|mixed|string
     */
    public function getNoProcessedTestingsForPatientsList($patient)
    {
        return $this->createQueryBuilder('paT')
            ->leftJoin('paT.medicalHistory', 'mH')
            ->leftJoin('mH.patient', 'p')
            ->leftJoin('paT.prescriptionTesting', 'prT')
            ->leftJoin('p.AuthUser', 'u')
            ->andWhere('mH.enabled = true')
            ->andWhere('mH.dateEnd IS NULL')
            ->andWhere('u.enabled = true')
            ->andWhere('paT.hasResult = true')
            ->andWhere('paT.isProcessedByStaff = false')
            ->andWhere('p = :patient')
            ->setParameter('patient', $patient)
            ->getQuery()
            ->getResult();
    }

    /**
     * Gets Patient Testings if PatientTesting has no results
     * @param $patient
     * @return int|mixed|string
     */
    public function getNoResultsTestingsForPatientsList($patient)
    {
        return $this->createQueryBuilder('paT')
            ->leftJoin('paT.medicalHistory', 'mH')
            ->leftJoin('mH.patient', 'p')
            ->leftJoin('paT.prescriptionTesting', 'prT')
            ->leftJoin('p.AuthUser', 'u')
            ->andWhere('mH.enabled = true')
            ->andWhere('mH.dateEnd IS NULL')
            ->andWhere('u.enabled = true')
            ->andWhere('p = :patient')
            ->andWhere('paT.hasResult = false')
            ->andWhere('prT.plannedDate <= :dateTimeNow')
            ->setParameter('dateTimeNow', new DateTime('now'))
            ->setParameter('patient', $patient)
            ->select('paT')
            ->getQuery()
            ->getResult();
    }

    /**
     * Gets Patients`ids array if PatientTesting is processed by staff
     * @return int|mixed|string
     */
    public function getProcessedResultsTestings()
    {
        return $this->createQueryBuilder('paT')
            ->leftJoin('paT.medicalHistory', 'mH')
            ->leftJoin('mH.patient', 'p')
            ->leftJoin('paT.prescriptionTesting', 'prT')
            ->leftJoin('p.AuthUser', 'u')
            ->andWhere('mH.enabled = true')
            ->andWhere('mH.dateEnd IS NULL')
            ->andWhere('u.enabled = true')
            ->andWhere('paT.hasResult = true')
            ->andWhere('paT.isProcessedByStaff = true')
            ->select('p.id')
            ->distinct()
            ->getQuery()
            ->getResult();
    }

    /**
     * Gets Patients`ids array if PatientTesting has result of testing but is not processed by staff
     * @return int|mixed|string
     */
    public function getNoProcessedTestings()
    {
        return $this->createQueryBuilder('paT')
            ->leftJoin('paT.medicalHistory', 'mH')
            ->leftJoin('mH.patient', 'p')
            ->leftJoin('paT.prescriptionTesting', 'prT')
            ->leftJoin('p.AuthUser', 'u')
            ->andWhere('mH.enabled = true')
            ->andWhere('mH.dateEnd IS NULL')
            ->andWhere('u.enabled = true')
            ->andWhere('paT.hasResult = true')
            ->andWhere('paT.isProcessedByStaff = false')
            ->select('p.id')
            ->distinct()
            ->getQuery()
            ->getResult();
    }

    /**
     * Gets Patients`ids array if PatientTesting has no results
     * @return int|mixed|string
     */
    public function getNoResultsTestings()
    {
        return $this->createQueryBuilder('paT')
            ->leftJoin('paT.medicalHistory', 'mH')
            ->leftJoin('mH.patient', 'p')
            ->leftJoin('paT.prescriptionTesting', 'prT')
            ->leftJoin('p.AuthUser', 'u')
            ->andWhere('mH.enabled = true')
            ->andWhere('mH.dateEnd IS NULL')
            ->andWhere('u.enabled = true')
            ->andWhere('paT.hasResult = false')
            ->andWhere('prT.plannedDate <= :dateTimeNow')
            ->select('p.id')
            ->distinct()
            ->setParameter('dateTimeNow', new DateTime('now'))
            ->getQuery()
            ->getResult();
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
