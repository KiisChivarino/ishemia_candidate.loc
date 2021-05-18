<?php

namespace App\Services\EntityActions\Editor;

use App\Services\EntityActions\Core\AbstractEditorService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class StaffEditorService
 * @package App\Services\EntityActions\Editor
 */
class StaffEditorService extends AbstractEditorService
{
    /**
     * StaffEditorService constructor.
     * @param EntityManagerInterface $entityManager
     * @param $entity
     * @throws Exception
     */
    public function __construct(EntityManagerInterface $entityManager, $entity)
    {
        parent::__construct($entityManager, $entity);
    }

    /**
     * @inheritDoc
     */
    protected function configureOptions(): void
    {
    }
}