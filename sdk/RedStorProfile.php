<?php
namespace RedStor\SDK;

use Predis\Profile\RedisVersion320;
use RedStor\SDK\Predis\Commands\Restart;

class RedStorProfile extends RedisVersion320
{
    public function getSupportedCommands(){
        return array_merge(parent::getSupportedCommands(), [
            'RESTART' => Restart::class
        ]);
    }
}