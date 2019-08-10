<?php

namespace RedStor\SDK\Types;

class ForeignKeyType extends IntType implements TypeInterface
{
    public function getName(): string
    {
        return 'ForeignKey';
    }

    public function isPrimaryKey(): bool
    {
        return false;
    }

    public function isAutoIncrement(): bool
    {
        return false;
    }
}
