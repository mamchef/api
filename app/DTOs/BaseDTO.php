<?php

namespace App\DTOs;

abstract readonly class BaseDTO
{
    public function toArray(): array
    {
        $array = [];

        foreach (get_object_vars($this) as $key => $value) {
            if (!$value instanceof DoNotChange) {
                $array[$key] = $value;
            }
        }

        return $array;
    }

    public static function toDTO(array $data): static
    {
        $reflector = new \ReflectionClass(static::class);

        $params = [];

        foreach ($reflector->getConstructor()?->getParameters() ?? [] as $param) {
            $name = $param->getName();
            $hasKey = array_key_exists($name, $data);

            $params[$name] = $hasKey ? $data[$name] : new DoNotChange();
        }

        return $reflector->newInstanceArgs($params);
    }
}