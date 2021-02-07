<?php

namespace App\Services\EntityActions\Creator;

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
    /** @var PatientMedicineCreatorService $patientMedicineCreatorService */
    private $patientMedicineCreatorService;

    /**
     * PrescriptionCreatorService constructor.
     * @param PatientMedicineCreatorService $patientMedicineCreatorService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        PatientMedicineCreatorService $patientMedicineCreatorService,
        EntityManagerInterface $entityManager
    )
    {
        parent::__construct($entityManager);
        $this->patientMedicineCreatorService = $patientMedicineCreatorService;
    }

    /**
     * @throws Exception
     */
    protected function prepare(): void
    {
        /** @var PrescriptionMedicine $prescriptionMedicine */
        $prescriptionMedicine = $this->getEntity();
        $this->patientMedicineCreatorService->execute([
            'medicalHistory'=> $this->options['prescription']->getMedicalHistory(),
            'prescriptionMedicine' => $prescriptionMedicine,
        ]);
        $prescriptionMedicine
            ->setInclusionTime(new DateTime())
            ->setPrescription($this->options['prescription'])
            ->setPatientMedicine($this->patientMedicineCreatorService->getEntity());
    }

    /**
     * @throws Exception
     */
    protected function configureOptions(): void
    {
        $this->setEntityClass(PrescriptionMedicine::class);
        $this->addOptionCheck(Prescription::class, 'prescription');
    }
}