<?php

namespace App\Services\ControllerGetters;

use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\Form;
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

    private $form;

    /**
     * EntityActions constructor.
     *
     * @param object $entity
     * @param Request|null $request
     * @param ObjectManager|null $entityManager
     * @param FormInterface|null $form
     */
    public function __construct(object $entity, Request $request = null, ?ObjectManager $entityManager = null, ?FormInterface $form = null)
    {
        $this->entity = $entity;
        $this->entityManager = $entityManager;
        $this->request = $request;
        $this->form = $form;
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

    /**
     * @return Form|null
     */
    public function getForm()
    {
        return $this->form;
    }
}