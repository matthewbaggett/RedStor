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
        $name = end($classElem);
        $cleanedName = substr($name, 0, -4);

        return trim($cleanedName);
    }

    protected function getMaxLength(): int
    {
        return $this->getSqlLength() > 0 ? $this->getSqlLength() : PHP_INT_MAX;
    }
}
