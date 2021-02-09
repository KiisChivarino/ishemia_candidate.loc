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
     * Gets patients with NoResults patient testings count
     * @param Hospital|null $hospital
     * @return int|mixed|string
     */
    public function getNoResultsTestingsCount(?Hospital $hospital)
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
            sizeof($qb->select('p.id')
                ->distinct()
                ->getQuery()
                ->getResult());
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
     * Gets overdue patient testings count
     * @param $patientId
     * @return int|mixed|string
     */
    public function getOverdueTestingsCount($patientId)
    {
        return $this->countByPatientTestingId(
            $this->generateOverdueQueryBuilder(
                $this->createQueryBuilder('paT'),
                $patientId
            )
        );
    }

    /**
     * Gets planned patient testings count
     * @param $patientId
     * @return int|mixed|string
     */
    public function getPlannedTestingsCount($patientId)
    {
        return $this->countByPatientTestingId(
            $this->generatePlannedQueryBuilder(
                $this->createQueryBuilder('paT'),
                $patientId
            )
        );
    }

    /**
     * Gets no processed patient testings count
     * @param $patientId
     * @return int|mixed|string
     */
    public function getNoProcessedTestingsCount($patientId)
    {
        return $this->countByPatientTestingId(
            $this->generateNoProcessedQueryBuilder(
                $this->createQueryBuilder('paT'),
                $patientId
            )
        );

    }

    /**
     * Add count by patient testing id
     * @param $qb
     * @return int
     */
    private function countByPatientTestingId($qb): int
    {
        return sizeof($qb->select('paT.id')
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
     * @return int
     */
    private function selectDistinctPatients($qb)
    {
        return $qb->select('p.id')
            ->distinct()
            ->getQuery()
            ->getResult();
    }

    /**
     * Gets patient testings for Datatable
     * @param QueryBuilder $qb
     * @param $patientId
     * @param $analysisGroup
     * @return QueryBuilder
     */
    public function getPatientTestingsForDatatable(QueryBuilder $qb, $patientId, $analysisGroup): QueryBuilder
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
     * Gets closed patient testings for Datatable
     * @param QueryBuilder $qb
     * @param $patientId
     * @param $analysisGroup
     * @return QueryBuilder
     */
    public function getPatientTestingsHistoryForDatatable(QueryBuilder $qb, $patientId, $analysisGroup): QueryBuilder
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
     * Gets no processed patient testings for Datatable
     * @param QueryBuilder $qb
     * @param $patientId
     * @param $analysisGroup
     * @return QueryBuilder
     */
    public function getPatientTestingsNoProcessedForDatatable(QueryBuilder $qb, $patientId, $analysisGroup): QueryBuilder
    {
        $this->generateNoProcessedQueryBuilder(
            $this->generateQueryBuilderForDatatable($qb),
            $patientId
        );
        $this->generateAnslisysGroupFilter($qb, $analysisGroup);
        return $qb;
    }

    /**
     * Gets overdue patient testings for Datatable
     * @param QueryBuilder $qb
     * @param $patientId
     * @param $analysisGroup
     * @return QueryBuilder
     */
    public function getPatientTestingsOverdueForDatatable(QueryBuilder $qb, $patientId, $analysisGroup): QueryBuilder
    {
        $this->generateOverdueQueryBuilder(
            $this->generateQueryBuilderForDatatable($qb),
            $patientId
        );
        $this->generateAnslisysGroupFilter($qb, $analysisGroup);
        return $qb;
    }

    /**
     * Gets planned patient testings for Datatable
     * @param QueryBuilder $qb
     * @param $patientId
     * @param $analysisGroup
     * @return QueryBuilder
     */
    public function getPatientTestingsPlannedForDatatable(QueryBuilder $qb, $patientId, $analysisGroup): QueryBuilder
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
     * Generate Planned Query Builder For Patient Testings
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
     * Generate Overdue Query Builder For Patient Testings
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
     * Generate No Processed Query Builder For Patient Testings
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
     * Generates Query Builder For Datatable
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
     * Generates Anslisys Group Filter
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
