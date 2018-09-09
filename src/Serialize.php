<?php

namespace Begels;

class Serialize
{
    /**
     * @param object $object
     * @return array
     * @throws \ReflectionException
     */
    public static function serialize(object $object) : array
    {
        $data = [];
        $reflection = new \ReflectionClass($object);
        foreach ($reflection->getProperties() as $property) {
            $isPublic = $property->isPublic();
            $property->setAccessible(true);
            $value = $property->getValue($object);

            $name = strtolower(trim(preg_replace('/(?<=\w)(?=[A-Z])/',"_$1", $property->getName())));
            if (is_object($value)) {
                $data[$name] = self::serialize($value);
            } else {
                $data[$name] = $value;
            }


            if (!$isPublic) {
                $property->setAccessible(false);
            }
        }
        return $data;
    }
}
