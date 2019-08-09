<?php
namespace RedStor\SDK\Entities;

use Predis\Pipeline\Pipeline;

interface EntityInterface extends \JsonSerializable
{
    public static function Factory();

    public function create(Pipeline $pipeline) : Pipeline;
    public function delete(Pipeline $pipeline) : Pipeline;
}