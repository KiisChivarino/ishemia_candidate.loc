<?php

namespace App\Repository;

use App\Entity\AnalysisGroup;
use App\Entity\PatientTesting;
use Doctrine\ORM\QueryBuilder;

/**
 * Class PatientTestingRepository
 * @method PatientTesting|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientTesting|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientTesting[]    findAll()
 * @method PatientTesting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @package App\Repository
 */
class PatientTestingDatatableRepository extends PatientTestingRepository
{
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
            ->setParameter('patientId', $patientId);
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
