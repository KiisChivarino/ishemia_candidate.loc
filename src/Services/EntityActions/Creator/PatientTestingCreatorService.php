<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\PatientTesting;
use App\Entity\Prescription;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class CreatingPatientTestingService
 * @package App\Services\CreatingPatientTesting
 */
abstract class PatientTestingCreatorService extends AbstractCreatorService
{
    /** @var string Name of Prescription option */
    public const PRESCRIPTION_OPTION = 'prescription';

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
        $patientTesting = $this->getEntity();
        $patientTesting
            ->setIsProcessedByStaff(false)
            ->setMedicalHistory($this->options[self::PRESCRIPTION_OPTION]->getMedicalHistory());
    }

    protected function configureOptions(): void
    {
        $this->addOptionCheck(Prescription::class, self::PRESCRIPTION_OPTION);
    }
}