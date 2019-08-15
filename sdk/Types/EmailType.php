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

    public function validate($input): bool
    {
        return parent::validate($input)
            && false !== stripos($input, '@')
            && filter_var(trim($input), FILTER_VALIDATE_EMAIL) === $input
        ;
    }
}
