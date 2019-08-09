<?php
namespace RedStor\SDK;

use Predis\Client;
use Predis\Profile\Factory;
use RedStor\SDK\Entities\Model;

/**
 * Class RedStorClient
 * @package RedStor\SDK
 * @method mixed  restart()
 */
class RedStorClient extends Client
{
    public function __construct($parameters = null, $options = null)
    {
        Factory::define(RedStorProfile::class, RedStorProfile::class);
        if(!$options){
            $options = [];
        }
        $options = array_merge($options, [
            'profile' => RedStorProfile::class,
        ]);
        parent::__construct($parameters, $options);
    }

    public function rsCreateModel(Model $model){
        $model->create($this->pipeline())
                    ->flushPipeline();
    }

    public function rsDescribeModel(string $name) : Model {

    }
}
