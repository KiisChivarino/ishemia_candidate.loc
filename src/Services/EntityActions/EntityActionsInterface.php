<?php

namespace App\Services\EntityActions;

/**
 * Interface CreatorInterface
 * for classes, using
 *
 * @package App\Services\EntityActions\Creator
 */
interface EntityActionsInterface
{
    /**
     * Actions with entity before submitting and validating form
     * Usual includes adding options, create new entity if it is a form of new entity and set default values for entity
     * @param array $options
     * @return EntityActionsInterface
     */
    public function before(array $options = []): self;

    /**
     * Actions with entity after submitting and validating form
     * Usual prepares entity for persist, sets custom values for entity, all other actions, persists entity
     * @param array $options
     * @return EntityActionsInterface
     */
    public function after(array $options = []): self;

    /**
     * Executes before and after methods one after another with common options
     * @param array $options
     * @return EntityActionsInterface
     */
    public function execute(array $options = []): self;
}