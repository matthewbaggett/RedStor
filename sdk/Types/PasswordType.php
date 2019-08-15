<?php

namespace RedStor\SDK\Types;

class PasswordType extends StringType implements TypeInterface
{
    use SerializableType;
    use StandardType;

    public function getSqlLength(): int
    {
        return strlen(password_hash('length', PASSWORD_DEFAULT));
    }

    public function getSolrType(): string
    {
        return 'ignored';
    }
}
