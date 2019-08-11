<?php

namespace RedStor\SDK\Types;

trait StandardType
{
    public function isPrimaryKey(): bool
    {
        return false;
    }

    public function isAutoIncrement(): bool
    {
        return false;
    }

    public function isUnique(): bool
    {
        return false;
    }

    public function getName(): string
    {
        $class = get_called_class();
        $classElem = explode('\\', $class);
        $name = reset($classElem);

        return substr($name, 0, -4);
    }
}
