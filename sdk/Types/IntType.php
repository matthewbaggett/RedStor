<?php

namespace RedStor\SDK\Types;

class IntType implements TypeInterface
{
    use SerializableType;
    use StandardType;

    public function getSqlType(): string
    {
        return 'INT';
    }

    public function getSqlLength(): int
    {
        return 12;
    }

    public function getSolrType(): string
    {
        return 'pint';
    }

    public function validate($input): bool
    {
        return is_int($input)
            && strlen($input) <= $this->getMaxLength();
    }
}
