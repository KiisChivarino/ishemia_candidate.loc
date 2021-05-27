<?php

namespace App\Services\EntityActions\Editor;

use App\Entity\Patient;
use App\Services\EntityActions\Core\AbstractEditorService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class PatientEditorService
 * @package App\Services\EntityActions\Editor
 */
class PatientEditorService extends AbstractEditorService
{
    /**
     * PatientEditorService constructor.
     * @param EntityManagerInterface $entityManager
     * @param $entity
     * @throws Exception
     */
    public function __construct(EntityManagerInterface $entityManager, $entity)
    {
        parent::__construct($entityManager, $entity);
    }

    protected function prepare(): void
    {
        /** @var Patient $patient */
        $patient = $this->getEntity();
        $authUser = $patient->getAuthUser();
        $authUser->setRoles($authUser->getRoles()[0]);
    }

    protected function configureOptions(): void
    {
    }
}