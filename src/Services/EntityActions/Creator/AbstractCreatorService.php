<?php

namespace App\Services\EntityActions\Creator;

use App\Services\EntityActions\AbstractEntityActionsService;
use App\Services\EntityActions\EntityActionsInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class AbstractCreatorService
 * @package App\Services\EntityActions\Creator
 */
abstract class AbstractCreatorService extends AbstractEntityActionsService
{
    /**
     * AbstractCreatorService constructor.
     * @param EntityManagerInterface $entityManager
     * @param string $entityClass
     * @throws Exception
     */
    public function __construct(EntityManagerInterface $entityManager, string $entityClass)
    {
        parent::__construct($entityManager);
        $this->setEntityClass($entityClass);
    }

    /**
     * Actions with entity before submitting and validating form
     * @param array $options
     * @return EntityActionsInterface
     * @throws Exception
     */
    public function before(array $options = []): EntityActionsInterface
    {
        parent::before($options);
        $this->create();
        return $this;
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