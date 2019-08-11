<?php

namespace RedStor\SDK\Types;

class TextType extends StringType implements TypeInterface
{
    public function getSqlType(): string
    {
        return 'TEXT';
    }

    public function getSqlLength(): int
    {
        return -1;
    }
}
