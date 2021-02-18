<?php

namespace App\Repository;

use App\Entity\MedicalHistory;
use App\Entity\PatientTesting;
use App\Services\InfoService\MedicalHistoryInfoService;
use DateTime;
use Doctrine\ORM\QueryBuilder;
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
     * Gets Patient Testings if PatientTesting has no results
     * @param $patient
     * @return int|mixed|string
     */
    public function getNoResultsTestingsForPatientsList($patient)
    {
        return $this->generateStandardQueryBuilder()
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
     * Gets Patient Testings if PatientTesting has result but is not processed by staff
     * @param $patient
     * @return int|mixed|string
     */
    public function getNoProcessedTestingsForPatientsList($patient)
    {
        return $this->generateStandardQueryBuilder()
            ->andWhere('paT.hasResult = true')
            ->andWhere('paT.isProcessedByStaff = false')
            ->andWhere('p = :patient')
            ->setParameter('patient', $patient)
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

    /**
     * Gets Patients`ids array for PatientTestings processed by staff
     * @return int|mixed|string
     */
    public function getProcessedResultsTestings()
    {
        return $this->selectDistinctPatients($this->generateStandardQueryBuilder()
            ->andWhere('paT.hasResult = true')
            ->andWhere('paT.isProcessedByStaff = true'));
    }

    /**
     * Gets Patients`ids array for PatientTesting with result and not processed by staff
     * @return int|mixed|string
     */
    public function getNoProcessedTestings()
    {
        return $this->selectDistinctPatients($this->generateStandardQueryBuilder()
            ->andWhere('paT.hasResult = true')
            ->andWhere('paT.isProcessedByStaff = false'));
    }

    /**
     * Gets Patients`ids array for PatientTesting with no results
     * @return int|mixed|string
     */
    public function getNoResultsTestings()
    {
        return $this->selectDistinctPatients($this->generateStandardQueryBuilder()
            ->andWhere('paT.hasResult = false')
            ->andWhere('prT.plannedDate <= :dateTimeNow')
            ->setParameter('dateTimeNow', new DateTime('now')));
    }

    /**
     * Add select and distinct by patient id
     * @param $qb
     * @return mixed
     */
    private function selectDistinctPatients($qb)
    {
        return $qb->select('p.id')
            ->distinct()
            ->getQuery()
            ->getResult();
    }

    /**
     * Generate Standard Query Builder For Patient Testings Where MedicalHistory Is Current And User Is Enabled
     * @return QueryBuilder
     */
    protected function generateStandardQueryBuilder(): QueryBuilder
    {
        return $this->generateStandardJoinsAndWheres(
            $this->createQueryBuilder('paT')
        );
    }

    /**
     * Generates Standard Joins And Wheres For Patient Testings Where MedicalHistory Is Current And User Is Enabled
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    protected function generateStandardJoinsAndWheres(QueryBuilder $qb): QueryBuilder
    {
        return $qb
            ->leftJoin('paT.medicalHistory', 'mH')
            ->leftJoin('mH.patient', 'p')
            ->leftJoin('paT.prescriptionTesting', 'prT')
            ->leftJoin('paT.analysisGroup', 'aG')
            ->leftJoin('p.AuthUser', 'u')
            ->andWhere('mH.enabled = true')
            ->andWhere('mH.dateEnd IS NULL')
            ->andWhere('u.enabled = true');
    }

    /**
     * Generate Planned Query Builder For Patient Testings
     * @param $qb
     * @param $patientId
     * @return QueryBuilder
     */
    protected function generatePlannedQueryBuilder(QueryBuilder $qb, $patientId): QueryBuilder
    {
        return $this->generateStandardJoinsAndWheres($qb)
            ->andWhere('prT.plannedDate >= :dateTimeNow')
            ->andWhere('p.id = :patientId')
            ->andWhere('paT.hasResult = false')
            ->setParameter('patientId', $patientId)
            ->setParameter('dateTimeNow', new DateTime('now'));
    }

    /**
     * Generate Overdue Query Builder For Patient Testings
     * @param $qb
     * @param $patientId
     * @return QueryBuilder
     */
    protected function generateOverdueQueryBuilder(QueryBuilder $qb, $patientId): QueryBuilder
    {
        return $this->generateStandardJoinsAndWheres($qb)
            ->andWhere('prT.plannedDate < :dateTimeNow')
            ->andWhere('p.id = :patientId')
            ->andWhere('paT.hasResult = false')
            ->setParameter('patientId', $patientId)
            ->setParameter('dateTimeNow', new DateTime('now'));
    }

    /**
     * Generate No Processed Query Builder For Patient Testings
     * @param $qb
     * @param $patientId
     * @return QueryBuilder
     */
    protected function generateNoProcessedQueryBuilder(QueryBuilder $qb, $patientId): QueryBuilder
    {
        return $this->generateStandardJoinsAndWheres($qb)
            ->andWhere('p.id = :patientId')
            ->andWhere('paT.isProcessedByStaff = false')
            ->andWhere('paT.hasResult = true')
            ->setParameter('patientId', $patientId);
    }
}
