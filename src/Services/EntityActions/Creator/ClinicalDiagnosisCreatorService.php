<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\ClinicalDiagnosis;
use App\Services\EntityActions\Core\AbstractCreatorService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class ClinicalDiagnosisCreatorService
 * @package App\Services\EntityActions\Creator
 */
class ClinicalDiagnosisCreatorService extends AbstractCreatorService
{
    /**
     * ClinicalDiagnosisCreatorService constructor.
     * @param EntityManagerInterface $entityManager
     * @throws Exception
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, ClinicalDiagnosis::class);
    }

    /**
     * @inheritDoc
     */
    protected function configureOptions(): void
    {
    }
}