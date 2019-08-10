<?php

namespace RedStor\SDK;

use Predis\Profile\RedisVersion320;
use RedStor\SDK\Predis\Commands;

class RedStorProfile extends RedisVersion320
{
    public function getSupportedCommands()
    {
        return array_merge(parent::getSupportedCommands(), [
            'RESTART' => Commands\Restart::class,
            'MODELCREATE' => Commands\ModelCreate::class,
            'MODELDESCRIBE' => Commands\ModelDescribe::class,
            'MODELADDCOLUMN' => Commands\ModelAddColumn::class,
        ]);
    }
}
