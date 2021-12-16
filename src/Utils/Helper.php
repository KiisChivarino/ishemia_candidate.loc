<?php


namespace App\Utils;


use ReflectionClass;

class Helper
{
    /**
     *
     * @param object $object
     * @return string
     */
    public static function getShortLowerClassName(object $object): string {
        return strtolower((new ReflectionClass($object))->getShortName());
    }
}