<?php

namespace RedStor\SDK\Predis\Commands;

use Predis\Command\Command;

class ModelAddColumn extends Command
{
    public function getId()
    {
        return 'MODELADDCOLUMN';
    }
}
