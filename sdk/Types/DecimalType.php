<?php
namespace RedStor\SDK\Types;

class DecimalType
    implements TypeInterface
{
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
        return "DECIMAL";
    }

    public function getSqlLength(): int
    {
        return 18;
    }

    public function getSolrType(): string
    {
        // TODO: Implement getSolrType() method.
    }

}