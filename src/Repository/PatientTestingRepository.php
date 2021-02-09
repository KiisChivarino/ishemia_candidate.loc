<?php

namespace App\Repository;

use App\Entity\AnalysisGroup;
use App\Entity\Hospital;
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
     * @param Hospital|null $hospital
     * @return int|mixed|string
     */
    public function getNoResultsTestingsMenu(?Hospital $hospital)
    {
        $qb = $this->generateStandardQueryBuilder()
            ->andWhere('paT.hasResult = false')
            ->andWhere('prT.plannedDate <= :dateTimeNow')
            ->setParameter('dateTimeNow', new DateTime('now'));
        if (!is_null($hospital)) {
            $qb->andWhere('p.hospital =:patientHospital')
                ->setParameter('patientHospital', $hospital);
        }
        return
            $qb->select('count(p.id)')
                ->distinct()
                ->getQuery()
                ->getSingleScalarResult();
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
     * Gets Patient Testings if PatientTesting has result of testing but is not processed by staff
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
     * Gets overdue testings
     * @param $patientId
     * @return int|mixed|string
     */
    public function getOverdueTestingsMenu($patientId)
    {
        return $this->countByPatientTestingId(
            $this->generateOverdueQueryBuilder(
                $this->createQueryBuilder('paT'),
                $patientId
            )
        );
    }

    /**
     * Gets planned testings
     * @param $patientId
     * @return int|mixed|string
     */
    public function getPlannedTestingsMenu($patientId)
    {
        return $this->countByPatientTestingId(
            $this->generatePlannedQueryBuilder(
                $this->createQueryBuilder('paT'),
                $patientId
            )
        );
    }

    /**
     * Gets no processed testings
     * @param $patientId
     * @return int|mixed|string
     */
    public function getNoProcessedTestingsMenu($patientId)
    {
        return $this->countByPatientTestingId(
            $this->generateNoProcessedQueryBuilder(
                $this->createQueryBuilder('paT'),
                $patientId
            )
        );

    }

    private function countByPatientTestingId($qb)
    {
        return $qb->select('count(paT.id)')
            ->distinct()
            ->getQuery()
            ->getSingleScalarResult();
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
     * Gets Patients`ids array if PatientTesting is processed by staff
     * @return int|mixed|string
     */
    public function getProcessedResultsTestings()
    {
        return $this->generateStandardQueryBuilder()
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
        return $this->generateStandardQueryBuilder()
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
        return $this->generateStandardQueryBuilder()
            ->andWhere('paT.hasResult = false')
            ->andWhere('prT.plannedDate <= :dateTimeNow')
            ->select('p.id')
            ->distinct()
            ->setParameter('dateTimeNow', new DateTime('now'))
            ->getQuery()
            ->getResult();
    }

    /**
     * @param QueryBuilder $qb
     * @param $patientId
     * @param $analysisGroup
     * @return QueryBuilder
     */
    public function patientTestingsForDatatable(QueryBuilder $qb, $patientId, $analysisGroup): QueryBuilder
    {
        $this->generateStandardJoinsAndWheres(
            $this->generateQueryBuilderForDatatable($qb)
        )
            ->andWhere('u.enabled = :val')
            ->andWhere('p.id = :patientId')
            ->setParameter('patientId', $patientId)
            ->setParameter('val', true);
        $this->generateAnslisysGroupFilter($qb, $analysisGroup);
        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param $patientId
     * @param $analysisGroup
     * @return QueryBuilder
     */
    public function patientTestingsHistoryForDatatable(QueryBuilder $qb, $patientId, $analysisGroup): QueryBuilder
    {
        $this->generateStandardJoinsAndWheres(
            $this->generateQueryBuilderForDatatable($qb)
        )
            ->andWhere('p.id = :patientId')
            ->andWhere('paT.isProcessedByStaff = true')
            ->andWhere('paT.hasResult = true')
            ->setParameter('patientId', $patientId)
            ->setParameter('val', true);
        $this->generateAnslisysGroupFilter($qb, $analysisGroup);
        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param $patientId
     * @param $analysisGroup
     * @return QueryBuilder
     */
    public function patientTestingsNoProcessedForDatatable(QueryBuilder $qb, $patientId, $analysisGroup): QueryBuilder
    {
        $this->generateNoProcessedQueryBuilder(
            $this->generateQueryBuilderForDatatable($qb),
            $patientId
        );
        $this->generateAnslisysGroupFilter($qb, $analysisGroup);
        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param $patientId
     * @param $analysisGroup
     * @return QueryBuilder
     */
    public function patientTestingsOverdueForDatatable(QueryBuilder $qb, $patientId, $analysisGroup): QueryBuilder
    {
        $this->generateOverdueQueryBuilder(
            $this->generateQueryBuilderForDatatable($qb),
            $patientId
        );
        $this->generateAnslisysGroupFilter($qb, $analysisGroup);
        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param $patientId
     * @param $analysisGroup
     * @return QueryBuilder
     */
    public function patientTestingsPlannedForDatatable(QueryBuilder $qb, $patientId, $analysisGroup): QueryBuilder
    {
        $this->generatePlannedQueryBuilder(
            $this->generateQueryBuilderForDatatable($qb),
            $patientId
        );
        $this->generateAnslisysGroupFilter($qb, $analysisGroup);
        return $qb;
    }

    /**
     * Generate Standard Query Builder For Patient Testings Where MedicalHistory Is Current And User Is Enabled
     * @return QueryBuilder
     */
    private function generateStandardQueryBuilder(): QueryBuilder
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
    private function generateStandardJoinsAndWheres(QueryBuilder $qb): QueryBuilder
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
     * Generate Standard Query Builder For Patient Testings Where MedicalHistory Is Current And User Is Enabled
     * @param $qb
     * @param $patientId
     * @return QueryBuilder
     */
    private function generatePlannedQueryBuilder(QueryBuilder $qb, $patientId): QueryBuilder
    {
        return $this->generateStandardJoinsAndWheres($qb)
            ->andWhere('prT.plannedDate >= :dateTimeNow')
            ->andWhere('p.id = :patientId')
            ->andWhere('paT.hasResult = false')
            ->setParameter('patientId', $patientId)
            ->setParameter('dateTimeNow', new DateTime('now'));
    }

    /**
     * Generate Overdue Query Builder For Patient Testings Where MedicalHistory Is Current And User Is Enabled
     * @param $qb
     * @param $patientId
     * @return QueryBuilder
     */
    private function generateOverdueQueryBuilder(QueryBuilder $qb, $patientId): QueryBuilder
    {
        return $this->generateStandardJoinsAndWheres($qb)
            ->andWhere('prT.plannedDate < :dateTimeNow')
            ->andWhere('p.id = :patientId')
            ->andWhere('paT.hasResult = false')
            ->setParameter('patientId', $patientId)
            ->setParameter('dateTimeNow', new DateTime('now'));
    }

    /**
     * Generate Overdue Query Builder For Patient Testings Where MedicalHistory Is Current And User Is Enabled
     * @param $qb
     * @param $patientId
     * @return QueryBuilder
     */
    private function generateNoProcessedQueryBuilder(QueryBuilder $qb, $patientId): QueryBuilder
    {
        return $this->generateStandardJoinsAndWheres($qb)
            ->andWhere('p.id = :patientId')
            ->andWhere('paT.isProcessedByStaff = false')
            ->andWhere('paT.hasResult = true')
            ->setParameter('patientId', $patientId);
    }

    /**
     * Generate Overdue Query Builder For Patient Testings Where MedicalHistory Is Current And User Is Enabled
     * @param $qb
     * @return QueryBuilder
     */
    private function generateQueryBuilderForDatatable(QueryBuilder $qb): QueryBuilder
    {
        return $qb
            ->select('paT')
            ->from(PatientTesting::class, 'paT');
    }

    /**
     * Generate Overdue Query Builder For Patient Testings Where MedicalHistory Is Current And User Is Enabled
     * @param QueryBuilder $qb
     * @param AnalysisGroup|null $analysisGroup
     * @return QueryBuilder
     */
    private function generateAnslisysGroupFilter(QueryBuilder $qb, $analysisGroup = null): QueryBuilder
    {
        return !is_null($analysisGroup) && $analysisGroup != "" ? $qb
            ->andWhere('paT.analysisGroup = :valAnalysisGroup')
            ->setParameter('valAnalysisGroup', $analysisGroup) : $qb;
    }
}
