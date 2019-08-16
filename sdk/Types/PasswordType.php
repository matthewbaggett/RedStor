<?php

namespace RedStor\SDK\Types;

class PasswordType extends StringType implements TypeInterface
{
    use SerializableType;
    use StandardType;

    public function getSqlLength(): int
    {
        // So while password_hash() using PASSWORD_DEFAULT returns
        // 60 characters, it may not in the future, when PASSWORD_DEFAULT gets changed!
        return 255;
    }

    public function getSolrType(): string
    {
        return 'ignored';
    }
}
