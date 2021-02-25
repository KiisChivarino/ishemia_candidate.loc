<?php

namespace App\Services\EntityActions\Editor;

use App\Services\EntityActions\AbstractEntityActionsService;
use Exception;

/**
 * Class AbstractEditService
 * @package App\Services\EntityActions\Editor
 */
abstract class AbstractEditorService extends AbstractEntityActionsService
{
    /**
     * @param array $options
     * @param null $entity
     * @throws Exception
     */
    public function before(array $options = [], $entity = null): void
    {
        parent::before($options, $entity);
        $this->setEntity($entity);
    }
}