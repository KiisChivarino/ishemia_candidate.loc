<?php

namespace App\Services\Creator;

use App\Entity\PatientAppointment;
use App\Entity\PlanAppointment;
use App\Entity\Prescription;
use App\Entity\PrescriptionAppointment;
use App\Entity\Staff;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * Class PrescriptionAppointmentCreatorService
 * @package App\Services\Creator
 */
class PrescriptionAppointmentCreatorService
{
    /** @var FlashBagInterface $flashBag */
    protected $flashBag;

    /**
     * PrescriptionAppointmentCreatorService constructor.
     * @param FlashBagInterface $flashBag
     */
    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    /**
     * Create PrescriptionAppointment entity object
     * @param Staff $staff
     * @param Prescription $prescription
     * @param PatientAppointment $patientAppointment
     * @param PlanAppointment $planAppointment
     * @return PrescriptionAppointment
     */
    public function createPrescriptionAppointment(
        Staff $staff,
        Prescription $prescription,
        PatientAppointment $patientAppointment,
        PlanAppointment $planAppointment
    ){
        return (new PrescriptionAppointment())
            ->setStaff($staff)
            ->setEnabled(true)
            ->setInclusionTime(new DateTime())
            ->setConfirmedByStaff(false)
            ->setPrescription($prescription)
            ->setPatientAppointment($patientAppointment)
            ->setPlannedDateTime($this->getAppointmentPlannedDate($planAppointment));
    }

    /**
     * Get planned date of appointment
     * @param PlanAppointment $planAppointment
     * @return DateTime|null
     */
    protected function getAppointmentPlannedDate(PlanAppointment $planAppointment): ?DateTime
    {
        try {
            if (!$plannedDate = CreatorHelper::getPlannedDate(
                new DateTime(),
                (int)(int)$planAppointment->getTimeRangeCount(),
                (int)(int)$planAppointment->getTimeRange()->getMultiplier(),
                $planAppointment->getTimeRange()->getDateInterval()->getFormat()
            )) {
                throw new Exception('Не удалось добавить дату приема по плану!');
            }
        } catch (Exception $e) {
            $this->flashBag->add('error', $e);
            return null;
        }
        return $plannedDate;
    }
}