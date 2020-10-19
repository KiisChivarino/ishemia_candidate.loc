<?php

namespace App\Repository;

use App\Entity\AuthUser;
use App\Entity\MedicalHistory;
use App\Entity\Patient;
use App\Entity\PatientAppointment;
use App\Entity\PatientTesting;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;

/**
 * @method Patient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Patient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Patient[]    findAll()
 * @method Patient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientRepository extends AppRepository
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
     * Persist patient
     *
     * @param Patient $patient
     * @param AuthUser $authUser
     * @param MedicalHistory $medicalHistory
     * @param PatientAppointment $patientAppointment
     *
     * @throws ORMException
     */
    public function persistPatient(
        Patient $patient,
        AuthUser $authUser,
        MedicalHistory $medicalHistory,
        PatientAppointment $patientAppointment
    ): void {
        $patient->setAuthUser($authUser);
        $this->_em->getRepository(MedicalHistory::class)->persistMedicalHistory($medicalHistory);
        $this->_em->getRepository(PatientAppointment::class)->persistPatientAppointment($patientAppointment);
        $this->_em->getRepository(PatientTesting::class)->persistPatientTestsByPlan($medicalHistory);
        $this->_em->getRepository(PatientAppointment::class)->persistPatientAppointmentsByPlan($medicalHistory);
        $this->_em->persist($patient);
    }
}
