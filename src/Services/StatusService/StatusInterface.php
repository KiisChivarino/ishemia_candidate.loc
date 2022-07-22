<?php

namespace App\Services\StatusService;

/**
 * Interface StatusInterface
 * @package App\Services\StatusService
 */
interface StatusInterface
{
    /**
     * Check if the status matches the current entity
     * @return mixed
     */
    public function matchStatus():bool;
}
