<?php

namespace App\Services\StatusService;

/**
 * Class StatusService
 * @package App\Services\StatusService
 */
abstract class Status implements StatusInterface
{
    /** @var  */
    protected $entity;

    /** @var  */
    protected $condition;

    /** @var StatusRender */
    protected $statusRender;

    /**
     * StatusService constructor.
     * @param $entity
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return StatusRender
     */
    public function getStatusRender(): StatusRender
    {
        return $this->statusRender;
    }

    /**
     * @param mixed $statusRender
     */
    public function setStatusRender($statusRender): void
    {
        $this->statusRender = $statusRender;
    }
}
