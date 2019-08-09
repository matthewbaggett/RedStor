<?php
namespace RedStor\SDK\Types;

class IntType
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
        return "INT";
    }

    public function getSqlLength(): int
    {
        return 12;
    }

    public function getSolrType(): string
    {
        // TODO: Implement getSolrType() method.
    }

}