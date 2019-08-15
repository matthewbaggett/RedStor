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

    public function validate($input): bool
    {
        return is_string($input) && strlen($input) <= $this->getMaxLength();
    }
}
