<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\PatientAppointment;
use App\Entity\Prescription;
use App\Entity\PrescriptionAppointment;
use App\Services\EntityActions\Core\AbstractCreatorService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class PrescriptionAppointmentCreatorService
 * @package App\Services\EntityActions\Creator
 */
class PrescriptionAppointmentCreatorService extends AbstractCreatorService
{
    /**
     * @const string
     */
    public const STAFF_OPTION = 'staff';

    /**
     * @const string
     */
    public const PRESCRIPTION_OPTION = 'prescription';

    /**
     * @const string
     */
    public const PATIENT_APPOINTMENT_OPTION = 'patientAppointment';

    /**
     * PrescriptionTestingCreatorService constructor.
     * @param EntityManagerInterface $entityManager
     * @throws Exception
     */
    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        parent::__construct($entityManager, PrescriptionAppointment::class);
    }

    protected function prepare(): void
    {
        /** @var PrescriptionAppointment $prescriptionAppointment */
        $prescriptionAppointment = $this->getEntity();
        $prescriptionAppointment
            ->setInclusionTime(new DateTime())
            ->setConfirmedByStaff(false)
            ->setPrescription($this->options[self::PRESCRIPTION_OPTION])
            ->setPatientAppointment($this->options[self::PATIENT_APPOINTMENT_OPTION]);
    }

    /**
     * @throws Exception
     */
    protected function configureOptions(): void
    {
        $this->addOptionCheck(Prescription::class, self::PRESCRIPTION_OPTION);
        $this->addOptionCheck(PatientAppointment::class, self::PATIENT_APPOINTMENT_OPTION);
    }
}