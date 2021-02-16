<?php

namespace App\Repository;

use App\Entity\Hospital;
use App\Entity\PatientTesting;
use DateTime;

/**
 * Class PatientTestingRepository
 * @method PatientTesting|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientTesting|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientTesting[]    findAll()
 * @method PatientTesting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @package App\Repository
 */
class PatientTestingCounterRepository extends PatientTestingRepository
{
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
}
