<?php

namespace RedStor\SDK\Types;

class PasswordType implements TypeInterface
{
    use SerializableType;
    use StandardType;

    public function getSqlType(): string
    {
        return 'VARCHAR';
    }

    public function getSqlLength(): int
    {
        return strlen(password_hash('length', PASSWORD_DEFAULT));
    }

    public function getSolrType(): string
    {
        return 'ignored';
    }
}
