<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\MedicalHistory;
use App\Entity\PatientMedicine;
use App\Entity\PrescriptionMedicine;

use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class PatientMedicineCreatorService
 * @package App\Services\EntityActions\Creator
 */
class PatientMedicineCreatorService extends AbstractCreatorService
{
    /**
     * @var string
     * yaml:config/services/entityActions/doctor_office_entity_actions.yml
     */
    private $MEDICAL_HISTORY_OPTION;

    /**
     * @var string
     * yaml:config/services/entityActions/doctor_office_entity_actions.yml
     */
    private $PRESCRIPTION_MEDICINE;

    public function __construct(
        EntityManagerInterface $entityManager,
        string $medicalHistoryOption,
        string $prescriptionMedicine
    )
    {
        parent::__construct($entityManager);
        $this->MEDICAL_HISTORY_OPTION = $medicalHistoryOption;
        $this->PRESCRIPTION_MEDICINE = $prescriptionMedicine;
    }

    protected function prepare(): void
    {
        /** @var PatientMedicine $patientMedicine */
        $patientMedicine = $this->getEntity();
        $patientMedicine
            ->setMedicalHistory($this->options['medicalHistory'])
            ->setPrescriptionMedicine($this->options['prescriptionMedicine']);
    }

    /**
     * @throws Exception
     */
    protected function configureOptions(): void
    {
        $this->setEntityClass(PatientMedicine::class);
        $this->addOptionCheck(MedicalHistory::class, $this->MEDICAL_HISTORY_OPTION);
        $this->addOptionCheck(PrescriptionMedicine::class, $this->PRESCRIPTION_MEDICINE);
    }
}