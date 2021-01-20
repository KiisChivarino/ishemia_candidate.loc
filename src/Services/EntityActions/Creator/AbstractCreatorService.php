<?php

namespace App\Services\EntityActions\Creator;

use App\Services\EntityActions\AbstractEntityActionsService;
use Exception;

/**
 * Class AbstractCreatorService
 * @package App\Services\EntityActions\Creator
 */
class AbstractCreatorService extends AbstractEntityActionsService
{
    /**
     * Actions with entity before submitting and validating form
     * @param string $entityClass
     * @param array $options
     * @throws Exception
     */
    public function before(string $entityClass, array $options = []): void
    {
        parent::before($entityClass, $options);
        $this->create($entityClass);
    }

    /**
     * Create new entity
     * @param string $entityClass
     * @throws Exception
     */
    protected function create(string $entityClass): void
    {
        if (class_exists($entityClass)) {
            $this->setEntity(new $entityClass, $entityClass);
        } else {
            throw new Exception('Class ' . $entityClass . ' not found!');
        }
        if (method_exists($this->entity, 'setEnabled')) {
            $this->entity->setEnabled(true);
        }
    }
}