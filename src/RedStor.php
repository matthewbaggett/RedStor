<?php

namespace RedStor;

use ⌬\⌬;

class RedStor extends ⌬
{
    public const KEY_MODEL_LIST_SET             = '{RedStor:Meta:Models}';
    public const KEY_MODEL_COLUMN_LIST_SET      = '{RedStor:Meta:Models:%s}:Columns';
    public const KEY_MODEL_COLUMN_DEFINITION    = '{RedStor:Meta:Models:%s}:Column:%s';
    public const KEY_MODEL_COLUMN_NAME          = '{RedStor:Meta:Models:%s}:Column:%s:name';
    public const KEY_MODEL_COLUMN_TYPE          = '{RedStor:Meta:Models:%s}:Column:%s:type';
    public const KEY_MODEL_COLUMN_OPTIONS       = '{RedStor:Meta:Models:%s}:Column:%s:options';
    public const KEY_MODEL_ITEM                 = '{RedStor:Data:%s}:Items:%s';
    public const KEY_MODEL_ITEM_FIELD           = '{RedStor:Data:%s}:Items:%s:%s';
    public const KEY_MODEL_INDEX                = '{RedStor:Meta:Indexes:%s}:Index:%s';

    public function __construct($options = [])
    {
        $this->isSessionsEnabled = false;
        parent::__construct($options);
    }
}
