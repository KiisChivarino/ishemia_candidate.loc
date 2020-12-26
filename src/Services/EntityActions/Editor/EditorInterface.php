<?php

namespace App\Services\EntityActions\Editor;

/**
 * Interface EditorInterface
 * @package App\Services\EntityActions\Editor
 */
interface EditorInterface
{
    public function prepare();

    public function persist();
}