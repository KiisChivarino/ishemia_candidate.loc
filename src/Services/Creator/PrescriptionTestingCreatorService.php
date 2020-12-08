<?php

namespace App\Services\Creator;

use App\Entity\PatientTesting;
use App\Entity\PlanTesting;
use App\Entity\Prescription;
use App\Entity\PrescriptionTesting;
use App\Entity\Staff;
use DateTime;
use DateTimeInterface;
use Exception;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * Class PrescriptionTestingCreatorService
 * @package App\Services\Creator
 */
class PrescriptionTestingCreatorService
{
    /** @var string Property dateBegin of medical history */
    protected const MEDICAL_HISTORY_DATE_BEGIN_PROPERTY = 'dateBegin';

    /** @var string Property heartAttackDate of patient */
    protected const PATIENT_HEART_ATTACK_DATE_PROPERTY = 'heartAttackDate';

    /** @var FlashBagInterface $flashBag */
    protected $flashBag;

    /**
     * PrescriptionTestingCreatorService constructor.
     * @param FlashBagInterface $flashBag
     */
    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    /**
     * Create PrescriptionTesting entity object
     * @param Prescription $prescription
     * @param Staff $staff
     * @param PatientTesting $patientTesting
     * @param PlanTesting|null $planTesting
     * @return PrescriptionTesting
     * @throws Exception
     */
    public function createPrescriptionTesting(
        Prescription $prescription,
        Staff $staff,
        PatientTesting $patientTesting,
        PlanTesting $planTesting = null
    ): PrescriptionTesting
    {
        return (new PrescriptionTesting())
            ->setStaff($staff)
            ->setEnabled(true)
            ->setInclusionTime(new DateTime())
            ->setConfirmedByStaff(false)
            ->setPrescription($prescription)
            ->setPatientTesting($patientTesting)
            ->setPlannedDate($this->getTestingPlannedDate($planTesting, $patientTesting));
    }

    /**
     * Get planned date of testing
     * @param PlanTesting $planTesting
     * @param PatientTesting $patientTesting
     * @return DateTimeInterface|null
     * @throws Exception
     */
    protected function getTestingPlannedDate(PlanTesting $planTesting, PatientTesting $patientTesting): ?DateTimeInterface
    {
        $patientTesting->getMedicalHistory()->getDateBegin();
        try {
            switch ($planTesting->getStartingPoint()->getName()) {
                case self::MEDICAL_HISTORY_DATE_BEGIN_PROPERTY :
                    $startingPoint = $patientTesting->getMedicalHistory()->getDateBegin();
                    break;
                case self::PATIENT_HEART_ATTACK_DATE_PROPERTY:
                    $startingPoint = $patientTesting->getMedicalHistory()->getPatient();
                    break;
                default:
                    throw new Exception('Не удалось получить точку отсчета для обследования по плану!');
            }
            if (!$plannedDate = CreatorHelper::getPlannedDate(
                $startingPoint,
                (int)$planTesting->getTimeRangeCount(),
                (int)$planTesting->getTimeRange()->getMultiplier(),
                $planTesting->getTimeRange()->getDateInterval()->getFormat()
            )) {
                throw new Exception('Не удалось добавить планируемую дату обследования!');
            }
        } catch (Exception $e) {
            $this->flashBag->add('error', $e);
            return null;
        }
        return $plannedDate;
    }
}