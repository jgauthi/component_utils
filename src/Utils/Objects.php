<?php
namespace Jgauthi\Component\Utils;

use ReflectionClass;

class Objects
{
    static public function isUsedTrait(string $trait, $object): bool
    {
        if (is_object($object)) {
            $objectName = get_class($object);
        } elseif (is_string($object)) {
            $objectName = $object;
        } else {
            throw new \InvalidArgumentException('Second argument is not an object or a string');
        }

        return in_array($trait, array_keys((new ReflectionClass($objectName))->getTraits()));
    }

    // php 8 version (for later)
    /*static public function isUsedTrait(string $trait, object|string $object): bool
    {
        if (!is_string($object)) {
            $object = $object::class;
        }

        return in_array($trait, array_keys((new ReflectionClass($object))->getTraits()));
    }*/
}