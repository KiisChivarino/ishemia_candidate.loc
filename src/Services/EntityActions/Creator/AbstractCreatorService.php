<?php

namespace App\Services\EntityActions\Creator;

use App\Services\EntityActions\AbstractEntityActionsService;
use Exception;

/**
 * Class AbstractCreatorService
 * @package App\Services\EntityActions\Creator
 */
abstract class AbstractCreatorService extends AbstractEntityActionsService
{

    /**
     * Actions with entity before submitting and validating form
     * @param array $options
     * @param null $entity
     * @throws Exception
     */
    public function before(array $options = [], $entity = null): void
    {
        parent::before($options, $entity);
        $this->create();
    }

    /**
     * Creates entity end sets as property of entity actions service
     * @throws Exception
     */
    protected function create(): void
    {
        $this->setEntity(new $this->entityClass);
        if (method_exists($this->entity, 'setEnabled')) {
            $this->entity->setEnabled(true);
        }
    }
}