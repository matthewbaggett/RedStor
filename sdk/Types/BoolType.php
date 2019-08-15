<?php

namespace RedStor\SDK\Types;

class BoolType extends IntType implements TypeInterface
{
    public function getSqlType(): string
    {
        return 'BOOL';
    }

    public function getSqlLength(): int
    {
        return -1;
    }

    public function getSolrType(): string
    {
        return 'boolean';
    }

    public function validate($input): bool
    {
        return is_bool($input);
    }
}
