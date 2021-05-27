<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\PatientMedicine;
use App\Services\EntityActions\Core\AbstractCreatorService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class PatientMedicineCreatorService
 * @package App\Services\EntityActions\Creator
 */
class PatientMedicineCreatorService extends AbstractCreatorService
{
    /**
     * PatientMedicineCreatorService constructor.
     * @param EntityManagerInterface $entityManager
     * @throws Exception
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, PatientMedicine::class);
    }

    /**
     * @throws Exception
     */
    protected function configureOptions(): void
    {
    }
}