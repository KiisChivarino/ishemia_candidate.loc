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
     * @param $entity
     * @param array $options
     * @throws Exception
     */
    public function before($entity, array $options = []): void
    {
        parent::before($entity, $options);
        $this->create(get_class($entity));
    }

    /**
     * Create new entity
     * @param string $class
     * @throws Exception
     */
    protected function create(string $class){
        if(class_exists($class)){
            $this->setEntity(new $class, $class);
        }else{
            throw new Exception('Class '. $class.' not found!');
        }
        if (method_exists($this->entity, 'setEnabled')) {
            $this->entity->setEnabled(true);
        }
    }
}