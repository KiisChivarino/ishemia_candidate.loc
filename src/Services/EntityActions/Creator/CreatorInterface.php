<?php

namespace App\Services\EntityActions;

interface CreatorInterface
{
    public function create();

    public function prepare();

    public function persist();
}