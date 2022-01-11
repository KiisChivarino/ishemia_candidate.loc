<?php


namespace App\Utils;


use ReflectionClass;

class ReflectionClassHelper
{
    /**
     *
     * @param object $object
     * @return string
     */
    public static function getShortLowerClassName(object $object): string {
        return lcfirst((new ReflectionClass($object))->getShortName());
    }
}