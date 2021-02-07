<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\MedicalHistory;
use App\Entity\PatientMedicine;
use App\Entity\PrescriptionMedicine;
use DateTime;
use Exception;

/**
 * Class PatientMedicineCreatorService
 * @package App\Services\EntityActions\Creator
 */
class PatientMedicineCreatorService extends AbstractCreatorService
{

    protected function prepare(): void
    {
        /** @var PatientMedicine $patientMedicine */
        $patientMedicine = $this->getEntity();
        $patientMedicine
            ->setMedicalHistory($this->options['medicalHistory'])
            ->setInstruction('инструкция')
            ->setDateBegin(new DateTime())
            ->setMedicineName('Тестовый препарат')
            ->setPrescriptionMedicine($this->options['prescriptionMedicine'])
        ;
    }

    /**
     * @throws Exception
     */
    protected function configureOptions(): void
    {
        $this->setEntityClass(PatientMedicine::class);
        $this->addOptionCheck(MedicalHistory::class, 'medicalHistory');
        $this->addOptionCheck(PrescriptionMedicine::class, 'prescriptionMedicine');
    }
}