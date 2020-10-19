<?php

namespace App\Repository;

use App\Entity\MedicalHistory;
use App\Entity\MedicalRecord;
use App\Entity\Patient;
use App\Entity\PatientAppointment;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
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
     * PatientAppointmentRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientAppointment::class);
    }

    /**
     * @param Patient $patient
     *
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function getFirstAppointment(Patient $patient)
    {
        return $this->createQueryBuilder('a')
            ->where('a.enabled = true and a.medicalHistory = :medicalHistory')
            ->orderBy('a.appointmentTime', 'ASC')
            ->setMaxResults(1)
            ->setParameter('medicalHistory', $this->_em->getRepository(MedicalHistory::class)->getCurrentMedicalHistory($patient))
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param PatientAppointment $patientAppointment
     *
     * @throws ORMException
     */
    public function persistPatientAppointment(PatientAppointment $patientAppointment): void
    {
        $patientAppointment
            ->setEnabled(true)
            ->setMedicalRecord(
               $this->_em->getRepository(MedicalRecord::class)->getMedicalRecord($patientAppointment->getMedicalHistory())
            )
            ->setIsConfirmed(false)
            ->setAppointmentTime(new DateTime());
        $this->_em->persist($patientAppointment);
    }
}