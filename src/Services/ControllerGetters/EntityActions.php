<?php

namespace App\Services\ControllerGetters;

use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

class EntityActions
{
    /** @var object $entity */
    private $entity;
    /** @var Request|null $response */
    private $request;
    /** @var ObjectManager|null $entityManager */
    private $entityManager;

    /**
     * EntityActions constructor.
     *
     * @param object $entity
     * @param Request|null $request
     * @param ObjectManager|null $entityManager
     */
    public function __construct(object $entity, Request $request = null, ?ObjectManager $entityManager = null)
    {
        $this->entity = $entity;
        $this->entityManager = $entityManager;
        $this->request = $request;
    }

    /**
     * @return object
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return Request|null
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return ObjectManager|null
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }
}