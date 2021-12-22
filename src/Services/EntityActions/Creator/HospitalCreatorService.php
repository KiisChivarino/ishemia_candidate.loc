<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\Hospital;
use App\Services\EntityActions\Core\AbstractCreatorService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class HospitalCreatorService
 * @package App\Services\EntityActions\Creator
 */
class HospitalCreatorService extends AbstractCreatorService
{
    /**
     * HospitalCreatorService constructor.
     * @param EntityManagerInterface $entityManager
     * @throws Exception
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, Hospital::class);
    }

    /**
     * @throws Exception
     */
    protected function configureOptions(): void
    {
    }
}