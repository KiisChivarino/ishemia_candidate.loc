<?php

namespace App\Services\EntityActions\Creator;

use App\Services\EntityActions\AbstractEntityActionsService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class AbstractCreatorService
 * @package App\Services\EntityActions\Creator
 */
abstract class AbstractCreatorService extends AbstractEntityActionsService
{
    /** @var string */
    protected $entityClass;

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
     * @throws Exception
     */
    public function before(array $options = []): void
    {
        parent::before();
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

    /**
     * Sets entity class for creator service
     * @param string $entityClass
     * @throws Exception
     */
    protected function setEntityClass(string $entityClass): void
    {
        if (class_exists($entityClass)) {
            $this->entityClass = $entityClass;
        } else {
            throw new Exception('Class ' . $entityClass . ' does not exists!');
        }
    }
}