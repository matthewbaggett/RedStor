<?php
namespace RedStor\SDK\Predis\Commands;

use Predis\Command\Command;

class Restart extends Command
{
    public function getId()
    {
        return 'RESTART';
    }
}
