<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\ClinicalDiagnosis;
use App\Entity\MedicalHistory;
use App\Entity\Patient;
use App\Entity\PatientDischargeEpicrisis;
use App\Services\EntityActions\Core\AbstractCreatorService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class MedicalHistoryCreatorService
 * @package App\Services\EntityActions\Creator
 */
class MedicalHistoryCreatorService extends AbstractCreatorService
{
    /** @var string Patient option */
    public const PATIENT_OPTION = 'patient';

    /** @var string Clinical diagnosis option */
    public const CLINICAL_DIAGNOSIS_OPTION = 'clinicalDiagnosis';

    /** @var string Discharge epicrisis option */
    public const DISCHARGE_EPICRISIS_OPTION = 'dischargeEpicrisis';

    /**
     * MedicalHistoryCreatorService constructor.
     * @param EntityManagerInterface $entityManager
     * @throws Exception
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, MedicalHistory::class);
    }

    protected function prepare(): void
    {
        /** @var MedicalHistory $medicalHistory */
        $medicalHistory = $this->getEntity();
        $medicalHistory
            ->setPatient($this->options[self::PATIENT_OPTION])
            ->setEnabled(true)
            ->setClinicalDiagnosis($this->options[self::CLINICAL_DIAGNOSIS_OPTION])
            ->setPatientDischargeEpicrisis($this->options[self::DISCHARGE_EPICRISIS_OPTION]);
    }

    protected function configureOptions(): void
    {
        $this->addOptionCheck(Patient::class, self::PATIENT_OPTION);
        $this->addOptionCheck(ClinicalDiagnosis::class, self::CLINICAL_DIAGNOSIS_OPTION);
        $this->addOptionCheck(PatientDischargeEpicrisis::class, self::DISCHARGE_EPICRISIS_OPTION);
    }
}