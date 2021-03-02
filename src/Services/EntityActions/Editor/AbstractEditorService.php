<?php

namespace App\Services\EntityActions\Editor;

use App\Services\EntityActions\AbstractEntityActionsService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class AbstractEditService
 * @package App\Services\EntityActions\Editor
 */
abstract class AbstractEditorService extends AbstractEntityActionsService
{
    /**
     * AbstractEditorService constructor.
     * @param EntityManagerInterface $entityManager
     * @param $entity
     * @throws Exception
     */
    public function __construct(EntityManagerInterface $entityManager, $entity)
    {
        parent::__construct($entityManager);
        $this->setEntity($entity);
    }
}