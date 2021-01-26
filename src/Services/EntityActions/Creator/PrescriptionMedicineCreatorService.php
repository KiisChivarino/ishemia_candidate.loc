<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\Prescription;
use App\Entity\PrescriptionMedicine;
use App\Services\ControllerGetters\EntityActions;
use DateTime;
use Exception;

/**
 * Class PrescriptionMedicineCreatorService
 * @package App\Services\EntityActions\Creator
 */
class PrescriptionMedicineCreatorService extends AbstractCreatorService
{
    /** @var PatientMedicineCreatorService $patientMedicineCreatorService */
    private $patientMedicineCreatorService;

    /**
     * PrescriptionCreatorService constructor.
     * @param PatientMedicineCreatorService $patientMedicineCreatorService
     */
    public function __construct(PatientMedicineCreatorService $patientMedicineCreatorService)
    {
        parent::__construct(PrescriptionMedicine::class);
        $this->patientMedicineCreatorService = $patientMedicineCreatorService;
    }

    /**
     * @param EntityActions $entityActions
     * @throws Exception
     */
    protected function prepare(EntityActions $entityActions): void
    {
        /** @var PrescriptionMedicine $prescriptionMedicine */
        $prescriptionMedicine = $this->getEntity();
        $this->patientMedicineCreatorService->execute($entityActions);
        $prescriptionMedicine
            ->setInclusionTime(new DateTime())
            ->setPrescription($this->options['prescription'])
            ->setPatientMedicine($this->patientMedicineCreatorService->getEntity());
    }

    protected function configureOptions()
    {
        $this->addOptionCheck(Prescription::class, 'prescription');
    }
}