<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\PatientTesting;
use App\Entity\Prescription;
use App\Entity\PrescriptionTesting;
use App\Entity\Staff;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class PrescriptionTestingCreatorService
 * @package App\Services\EntityActions\Creator
 */
class PrescriptionTestingCreatorService extends AbstractCreatorService
{
    /**
     * @var string
     * yaml:config/services/entityActions/doctor_office_entity_actions.yml
     */
    public static $STAFF_OPTION;

    /**
     * @var string
     * yaml:config/services/entityActions/doctor_office_entity_actions.yml
     */
    public static $PRESCRIPTION_OPTION;

    /**
     * @var string
     * yaml:config/services/entityActions/doctor_office_entity_actions.yml
     */
    public static $PATIENT_TESTING_OPTION;

    /**
     * PrescriptionTestingCreatorService constructor.
     * @param EntityManagerInterface $entityManager
     * @param string $staffOption
     * @param string $prescriptionOption
     * @param string $patientTestingOption
     * @throws Exception
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        string $staffOption,
        string $prescriptionOption,
        string $patientTestingOption
    )
    {
        parent::__construct($entityManager, PrescriptionTesting::class);
        self::$STAFF_OPTION = $staffOption;
        self::$PRESCRIPTION_OPTION = $prescriptionOption;
        self::$PATIENT_TESTING_OPTION = $patientTestingOption;
    }

    protected function prepare(): void
    {
        /** @var PrescriptionTesting $prescriptionTesting */
        $prescriptionTesting = $this->getEntity();
        $prescriptionTesting
            ->setInclusionTime(new DateTime())
            ->setPrescription($this->options[self::$PRESCRIPTION_OPTION])
            ->setPatientTesting($this->options[self::$PATIENT_TESTING_OPTION]);
    }

    /**
     * @throws Exception
     */
    protected function configureOptions(): void
    {
        $this->setEntityClass(PrescriptionTesting::class);
        $this->addOptionCheck(Prescription::class, self::$PRESCRIPTION_OPTION);
        $this->addOptionCheck(PatientTesting::class, self::$PATIENT_TESTING_OPTION);
    }
}