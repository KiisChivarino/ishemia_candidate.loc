<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\PatientMedicine;
use App\Entity\Prescription;
use App\Entity\PrescriptionMedicine;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class PrescriptionMedicineCreatorService
 * @package App\Services\EntityActions\Creator
 */
class PrescriptionMedicineCreatorService extends AbstractCreatorService
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
    public const PATIENT_MEDICINE_OPTION = 'patientMedicine';

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        parent::__construct($entityManager, PrescriptionMedicine::class);
    }

    /**
     * @throws Exception
     */
    protected function prepare(): void
    {
        /** @var PrescriptionMedicine $prescriptionMedicine */
        $prescriptionMedicine = $this->getEntity();
        $prescriptionMedicine
            ->setInclusionTime(new DateTime())
            ->setPrescription($this->options[self::PRESCRIPTION_OPTION])
            ->setPatientMedicine($this->options[self::PATIENT_MEDICINE_OPTION]);
    }

    /**
     * @throws Exception
     */
    protected function configureOptions(): void
    {
        $this->addOptionCheck(Prescription::class, self::PRESCRIPTION_OPTION);
        $this->addOptionCheck(PatientMedicine::class, self::PATIENT_MEDICINE_OPTION);
    }
}