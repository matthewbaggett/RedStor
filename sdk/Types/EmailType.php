<?php

namespace RedStor\SDK\Types;

class EmailType extends StringType
{
    use SerializableType;

    public function isUnique(): bool
    {
        return true;
    }

    public function getSqlLength(): int
    {
        return 320;
    }
}
