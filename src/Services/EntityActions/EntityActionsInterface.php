<?php

namespace App\Services\EntityActions;
use App\Services\ControllerGetters\EntityActions;

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
     * @param $entity
     * @param array $options
     */
    public function before($entity, array $options = []): void;

    /**
     * Actions with entity after submitting and validating form
     * Usual prepares entity for persist, sets custom values for entity, all other actions, persists entity
     * @param EntityActions $entityActions
     * @param array $options
     */
    public function after(EntityActions $entityActions, array $options = []): void;
}