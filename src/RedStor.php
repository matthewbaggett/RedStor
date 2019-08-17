<?php

namespace RedStor;

use ⌬\⌬;

class RedStor extends ⌬
{
    public const KEY_MODEL_LIST_SET = '{RedStor:Meta:Models}';
    public const KEY_MODEL_COLUMN_LIST_SET = '{RedStor:Meta:Models:%s}:Columns';
    public const KEY_MODEL_COLUMN_DEFINITION = '{RedStor:Meta:Models:%s}:Column:%s';
    public const KEY_MODEL_COLUMN_NAME = '{RedStor:Meta:Models:%s}:Column:%s:name';
    public const KEY_MODEL_COLUMN_TYPE = '{RedStor:Meta:Models:%s}:Column:%s:type';
    public const KEY_MODEL_COLUMN_OPTIONS = '{RedStor:Meta:Models:%s}:Column:%s:options';
    public const KEY_MODEL_ITEM = '{RedStor:Data:%s}:Items:%s';
    public const KEY_MODEL_ITEM_FIELD = '{RedStor:Data:%s}:Items:%s:%s';
    public const KEY_MODEL_INDEX = '{RedStor:Meta:Indexes:%s}:Index:%s';
    public const KEY_FLUSH_QUEUE = '{RedStor:Queues}:FlushToDB';
    public const KEY_AUTH_APP = 'RedStor:Auth:%s';
    public const KEY_LIMIT_RATELIMIT_REQUESTSPERHOUR = 'RedStor:RateLimit:%s:RequestsPerHour';
    public const KEY_LIMIT_RATELIMIT_REQUESTSPERHOUR_AVAILABLE = 'RedStor:RateLimit:%s:RequestsPerHourAvailable';

    public const CHANNEL_QUEUE_FLUSHTODB = 'RedStor:Queues:FlushToDB';
    public const CHANNEL_LOGIN_SUCCESS = 'RedStor:Login:Successes';
    public const CHANNEL_LOGIN_FAILURES = 'RedStor:Login:Failures';

    public const SILENCED_COMMANDS = ['PING'];

    public function __construct($options = [])
    {
        $this->isSessionsEnabled = false;
        parent::__construct($options);
    }
}
