<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\PatientTesting;
use App\Entity\PlanTesting;
use App\Entity\Prescription;
use App\Entity\PrescriptionTesting;
use App\Entity\Staff;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * Class PrescriptionTestingCreatorService
 * @package App\Services\EntityActions\Creator
 */
class PrescriptionTestingCreatorService extends AbstractCreatorService
{
    /** @var FlashBagInterface $flashBag */
    protected $flashBag;

    /**
     * @var string
     * yaml:config/services/entityActions/doctor_office_entity_actions.yml
     */
    private $STAFF_OPTION;

    /**
     * @var string
     * yaml:config/services/entityActions/doctor_office_entity_actions.yml
     */
    private $PRESCRIPTION_OPTION;

    /**
     * PrescriptionTestingCreatorService constructor.
     * @param EntityManagerInterface $entityManager
     * @param FlashBagInterface $flashBag
     * @param string $staffOption
     * @param string $prescriptionOption
     * @throws Exception
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        string $staffOption,
        string $prescriptionOption
    )
    {
        parent::__construct($entityManager, PrescriptionTesting::class);
        $this->flashBag = $flashBag;
        $this->STAFF_OPTION = $staffOption;
        $this->PRESCRIPTION_OPTION = $prescriptionOption;
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
    protected function getTestingPlannedDate(
        PlanTesting $planTesting,
        PatientTesting $patientTesting
    ): ?DateTimeInterface
    {
        try {
            if (!$plannedDate = CreatorHelper::getPlannedDate(
                CreatorHelper::getStartingPointDate(
                    $planTesting->getStartingPoint()->getName(),
                    clone $patientTesting->getMedicalHistory()->getDateBegin(),
                    clone $patientTesting->getMedicalHistory()->getPatient()->getHeartAttackDate()
                    ),
                (int) $planTesting->getTimeRangeCount(),
                (int) $planTesting->getTimeRange()->getMultiplier(),
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

    /**
     * @throws Exception
     */
    protected function configureOptions(): void
    {
        $this->setEntityClass(PrescriptionTesting::class);
        $this->addOptionCheck(Prescription::class, $this->STAFF_OPTION);
        $this->addOptionCheck(Staff::class, $this->PRESCRIPTION_OPTION);
    }
}