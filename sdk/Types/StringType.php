<?php

namespace RedStor\SDK\Types;

class StringType implements TypeInterface
{
    use SerializableType;
    use StandardType;

    public function getSqlType(): string
    {
        return 'VARCHAR';
    }

    public function getSqlLength(): int
    {
        return 255;
    }

    public function getSolrType(): string
    {
        return 'text_general';
    }
}
