<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\MedicalHistory;
use App\Entity\PatientTesting;
use App\Services\EntityActions\Core\AbstractCreatorService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class CreatingPatientTestingService
 * @package App\Services\CreatingPatientTesting
 */
abstract class PatientTestingCreatorService extends AbstractCreatorService
{
    /** @var string Name of Prescription option */
    public const MEDICAL_HISTORY_OPTION = 'medicalHistory';

    /**
     * PatientTestingCreatorService constructor.
     * @param EntityManagerInterface $entityManager
     * @throws Exception
     */
    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        parent::__construct($entityManager, PatientTesting::class);
    }

    protected function prepare(): void
    {
        /** @var PatientTesting $patientTesting */
        $patientTesting = $this->getEntity();
        $patientTesting
            ->setHasResult(false)
            ->setIsProcessedByStaff(false)
            ->setMedicalHistory($this->options[self::MEDICAL_HISTORY_OPTION]);
    }

    protected function configureOptions(): void
    {
        $this->addOptionCheck(MedicalHistory::class, self::MEDICAL_HISTORY_OPTION);
    }
}