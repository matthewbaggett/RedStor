<?php

namespace RedStor\SDK\Types;

class DecimalType implements TypeInterface
{
    use SerializableType;
    use StandardType;

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

    public function validate($input): bool
    {
        return is_numeric($input);
    }
}
