<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\PatientMedicine;
use App\Entity\Prescription;

use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class PatientMedicineCreatorService
 * @package App\Services\EntityActions\Creator
 */
class PatientMedicineCreatorService extends AbstractCreatorService
{

    /** @var string Name of Prescription option */
    public const PRESCRIPTION_OPTION = 'prescription';

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        parent::__construct($entityManager, PatientMedicine::class);
    }

    protected function prepare(): void
    {
        /** @var PatientMedicine $patientMedicine */
        $patientMedicine = $this->getEntity();
        $patientMedicine
            ->setEnabled(true);
    }

    /**
     * @throws Exception
     */
    protected function configureOptions(): void
    {
        $this->addOptionCheck(Prescription::class, self::PRESCRIPTION_OPTION);
    }
}