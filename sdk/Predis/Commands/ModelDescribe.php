<?php

namespace RedStor\SDK\Predis\Commands;

use Predis\Command\Command;

class ModelDescribe extends Command
{
    public function getId()
    {
        return 'MODELDESCRIBE';
    }
}
