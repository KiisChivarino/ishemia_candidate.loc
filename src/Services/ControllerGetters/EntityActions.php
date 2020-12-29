<?php

namespace App\Services\ControllerGetters;

use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class EntityActions
{
    /** @var object $entity */
    private $entity;
    /** @var Request|null $response */
    private $request;
    /** @var ObjectManager|null $entityManager */
    private $entityManager;
    /** @var FormInterface|null */
    private $form;

    /**
     * EntityActions constructor.
     *
     * @param object $entity
     * @param Request|null $request
     * @param ObjectManager|null $entityManager
     * @param FormInterface|null $form
     */
    public function __construct(
        object $entity,
        Request $request = null,
        ?ObjectManager $entityManager = null,
        ?FormInterface $form = null
    )
    {
        $this->entity = $entity;
        $this->entityManager = $entityManager;
        $this->request = $request;
        $this->form = $form;
    }

    /**
     * @return object
     */
    public function getEntity(): object
    {
        return $this->entity;
    }

    /**
     * @return Request|null
     */
    public function getRequest(): ?Request
    {
        return $this->request;
    }

    /**
     * @return ObjectManager|null
     */
    public function getEntityManager(): ?ObjectManager
    {
        return $this->entityManager;
    }

    /**
     * @return FormInterface|null
     */
    public function getForm(): ?FormInterface
    {
        return $this->form;
    }
}