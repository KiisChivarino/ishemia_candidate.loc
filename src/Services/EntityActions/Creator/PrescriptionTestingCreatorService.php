<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\PatientTesting;
use App\Entity\Prescription;
use App\Entity\PrescriptionTesting;
use App\Services\EntityActions\Core\AbstractCreatorService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class PrescriptionTestingCreatorService
 * @package App\Services\EntityActions\Creator
 */
class PrescriptionTestingCreatorService extends AbstractCreatorService
{
    /**
     * @const string
     */
    public const PRESCRIPTION_OPTION = 'prescription';

    /**
     * @const string
     */
    public const PATIENT_TESTING_OPTION = 'patientTesting';

    /**
     * PrescriptionTestingCreatorService constructor.
     * @param EntityManagerInterface $entityManager
     * @throws Exception
     */
    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        parent::__construct($entityManager, PrescriptionTesting::class);
    }

    protected function prepare(): void
    {
        /** @var PrescriptionTesting $prescriptionTesting */
        $prescriptionTesting = $this->getEntity();
        $prescriptionTesting
            ->setConfirmedByStaff(false)
            ->setPrescription($this->options[self::PRESCRIPTION_OPTION])
            ->setPatientTesting($this->options[self::PATIENT_TESTING_OPTION]);
    }

    /**
     * @throws Exception
     */
    protected function configureOptions(): void
    {
        $this->addOptionCheck(Prescription::class, self::PRESCRIPTION_OPTION);
        $this->addOptionCheck(PatientTesting::class, self::PATIENT_TESTING_OPTION);
    }
}