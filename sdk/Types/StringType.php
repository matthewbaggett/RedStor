<?php
namespace RedStor\SDK\Types;

class StringType
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
        return "VARCHAR";
    }

    public function getSqlLength(): int
    {
        return 255;
    }

    public function getSolrType(): string
    {
        // TODO: Implement getSolrType() method.
    }

}