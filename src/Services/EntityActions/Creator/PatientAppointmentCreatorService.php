<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\MedicalHistory;
use App\Entity\PatientAppointment;
use App\Entity\PrescriptionAppointment;
use App\Services\EntityActions\Core\AbstractCreatorService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class PatientAppointmentCreatorService
 * @package App\Services\EntityActions\Creator
 */
class PatientAppointmentCreatorService extends AbstractCreatorService
{
    /** @var string Name of Prescription appointment option */
    public const PRESCRIPTION_APPOINTMENT_OPTION = 'prescriptionAppointment';

    /** @var string Name of Staff option */
    public const STAFF_OPTION = 'staff';

    /** @var string Name of Medical History option */
    public const MEDICAL_HISTORY_OPTION = 'medicalHistory';

    /**
     * PatientTestingCreatorService constructor.
     * @param EntityManagerInterface $entityManager
     * @throws Exception
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, PatientAppointment::class);
    }

    protected function prepare(): void
    {
        /** @var PatientAppointment $patientAppointment */
        $patientAppointment = $this->getEntity();
        $patientAppointment
            ->setIsConfirmed(false)
            ->setIsFirst(false)
            ->setIsByPlan(false)
            ->setMedicalHistory($this->options[self::MEDICAL_HISTORY_OPTION])
            ->setPrescriptionAppointment($this->options[self::PRESCRIPTION_APPOINTMENT_OPTION])
            ->setStaff($patientAppointment->getPrescriptionAppointment()->getStaff());
    }

    protected function configureOptions(): void
    {
        $this->addOptionCheck(MedicalHistory::class, self::MEDICAL_HISTORY_OPTION);
        $this->addOptionCheck(PrescriptionAppointment::class, self::PRESCRIPTION_APPOINTMENT_OPTION);
    }
}