<?php

namespace RedStor\SDK\Types;

class DecimalType implements TypeInterface
{
    use SerializableType;

    public function getName(): string
    {
        return 'Decimal';
    }

    public function isPrimaryKey(): bool
    {
        return false;
    }

    public function isAutoIncrement(): bool
    {
        return false;
    }

    public function getSqlType(): string
    {
        return 'DECIMAL';
    }

    public function getSqlLength(): int
    {
        return 18;
    }

    public function getSolrType(): string
    {
        return 'pdouble';
    }
}
