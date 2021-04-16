<?php

namespace App\Services\EntityActions\Core;

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
        $this->setEntityClass(get_class($entity));
        $this->setEntity($entity);
    }
}