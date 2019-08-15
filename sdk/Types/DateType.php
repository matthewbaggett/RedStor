<?php

namespace RedStor\SDK\Types;

class DateType implements TypeInterface
{
    use SerializableType;
    use StandardType;

    public function getSqlType(): string
    {
        return 'DATETIME';
    }

    public function getSqlLength(): int
    {
        return -1;
    }

    public function getSolrType(): string
    {
        return 'pdate';
    }

    public function validate($input): bool
    {
        return $input instanceof \DateTime;
    }
}
