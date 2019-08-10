<?php

namespace RedStor\SDK\Predis\Commands;

use Predis\Command\Command;

class ModelCreate extends Command
{
    public function getId()
    {
        return 'MODELCREATE';
    }
}
