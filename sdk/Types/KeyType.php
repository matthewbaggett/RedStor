<?php

namespace RedStor\SDK\Types;

class KeyType extends IntType implements TypeInterface
{
    public function getName(): string
    {
        return 'Key';
    }

    public function isPrimaryKey(): bool
    {
        return true;
    }

    public function isAutoIncrement(): bool
    {
        return true;
    }
}
