<?php

namespace App\Utils;

use ReflectionClass;

/**
 * Methods to get info about class or object
 */
class ReflectionClassHelper
{
    /**
     *
     * @param object $object
     *
     * @return string
     */
    public static function getShortLowerClassName(object $object): string
    {
        return lcfirst((new ReflectionClass($object))->getShortName());
    }

    /**
     * Check is method exists for entity
     *
     * @param object|null $object
     * @param string $methodName
     *
     * @return bool
     */
    public static function isMethodExists(?object $object, string $methodName): bool
    {
        return is_object($object) && method_exists($object, $methodName);
    }
}