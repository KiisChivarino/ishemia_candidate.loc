<?php

namespace App\Services\EntityActions\Core\Builder;

use App\Services\EntityActions\Core\AbstractEditorService;
use Closure;

/**
 * Class EditorEntityActionsBuilder
 * contains EditorEntityActions object with options
 * @package App\Services\EntityActions\Builder
 */
class EditorEntityActionsBuilder extends EntityActionsBuilder
{
    /**
     * EditorEntityActionsBuilder constructor.
     * @param AbstractEditorService $editorService
     * @param array $beforeOptions
     * @param Closure|null $afterOptions
     */
    public function __construct(
        AbstractEditorService $editorService,
        array $beforeOptions = [],
        ?Closure $afterOptions = null
    )
    {
        parent::__construct($beforeOptions, $afterOptions);
        $this->setEntityActionsService($editorService);
    }
}