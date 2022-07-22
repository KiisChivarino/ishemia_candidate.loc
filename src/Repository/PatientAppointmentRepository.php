<?php

namespace App\Repository;

use App\Entity\MedicalHistory;
use App\Entity\PatientAppointment;
use Doctrine\ORM\NonUniqueResultException;
use DateTime;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PatientAppointmentRepository
 * @method PatientAppointment|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientAppointment|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientAppointment[]    findAll()
 * @method PatientAppointment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @package App\Repository
 */
class PatientAppointmentRepository extends AppRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, PatientAppointment::class);
    }

    /**
     * Получение первого приема пациента
     *
     * @param MedicalHistory $medicalHistory
     *
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function getFirstAppointment(MedicalHistory $medicalHistory): ?PatientAppointment
    {
        return $this->createQueryBuilder('a')
            ->where('a.enabled = true and a.medicalHistory = :medicalHistory and a.isFirst = true')
            ->setMaxResults(1)
            ->setParameter('medicalHistory', $medicalHistory)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Generates standard join and where statements for patient appointment
     * where medical history Is current and user is enabled
     *
     * @param QueryBuilder $qb
     * @param $patientId
     *
     * @return QueryBuilder
     */
    public function generateStandardJoinsAndWheres(QueryBuilder $qb, $patientId): QueryBuilder
    {
        return $qb
            ->leftJoin('paT.medicalHistory', 'mH')
            ->leftJoin('mH.patient', 'p')
            ->leftJoin('paT.prescriptionAppointment', 'prA')
            ->leftJoin('prA.prescription', 'pr')
            ->leftJoin('p.AuthUser', 'u')
            ->andWhere('mH.enabled = true')
            ->andWhere('mH.dateEnd IS NULL')
            ->andWhere('u.enabled = true')
            ->andWhere('p.id = :patientId')
            ->andWhere('pr.isCompleted = true')
            ->setParameter('patientId', $patientId);
    }

    /**
     * Get no processed patient appointments
     *
     * @param QueryBuilder $queryBuilder
     *
     * @return QueryBuilder
     */
    protected function getNoProcessedPatientAppointments(QueryBuilder $queryBuilder): QueryBuilder
    {
        return $queryBuilder
            ->andWhere('paT.isProcessedByStaff = :isProcessedByStaff')
            ->andWhere('paT.isMissed = :isMissed')
            ->andWhere('paT.isFirst = :isFirst')
            ->andWhere('prA.plannedDateTime <= :plannedDateTime')
            ->orderBy('prA.plannedDateTime', 'DESC')
            ->setParameter('plannedDateTime', new DateTime('now'))
            ->setParameter('isProcessedByStaff', false)
            ->setParameter('isFirst', false)
            ->setParameter('isMissed', false);
    }

    /**
     * Get is missed patient appointments
     *
     * @param QueryBuilder $queryBuilder
     *
     * @return QueryBuilder
     */
    protected function getHistoryPatientAppointments(QueryBuilder $queryBuilder): QueryBuilder
    {
        return $queryBuilder
            ->andWhere('paT.isProcessedByStaff = :isProcessedByStaff')
            ->setParameter('isProcessedByStaff', true)
            ->orderBy('prA.plannedDateTime', 'ASC');
    }
}
