<?php

namespace App\Services\EntityActions\Builder;

use App\Services\EntityActions\Editor\AbstractEditorService;

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
     * @param array $afterOptions
     */
    public function __construct(
        AbstractEditorService $editorService,
        array $beforeOptions = [],
        array $afterOptions = []
    )
    {
        parent::__construct($beforeOptions, $afterOptions);
        $this->setEntityActionsService($editorService);
    }
}