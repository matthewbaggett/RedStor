<?php

namespace RedStor;

use ⌬\⌬;

class RedStor extends ⌬
{
    public const KEY_MODEL_LIST_SET = 'RedStor:Models';
    public const KEY_MODEL_COLUMN_LIST_SET = '{RedStor:Models:%s}:Columns';
    public const KEY_MODEL_COLUMN_DEFINITION = '{RedStor:Models:%s}:Column:%s';
    public const KEY_MODEL_COLUMN_NAME = '{RedStor:Models:%s}:Column:%s:name';
    public const KEY_MODEL_COLUMN_TYPE = '{RedStor:Models:%s}:Column:%s:type';
    public const KEY_MODEL_COLUMN_OPTIONS = '{RedStor:Models:%s}:Column:%s:options';
}
