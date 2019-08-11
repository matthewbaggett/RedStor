<?php

namespace RedStor\SDK;

use Predis\Client;
use Predis\Profile\Factory;
use RedStor\SDK\Entities\Model;

/**
 * Class RedStorClient.
 *
 * @method mixed restart()
 * @method mixed modelCreate(string $modelName)
 * @method mixed modelAddColumn(string $modelName, string $columnName, string $columnType, string $optionsJsonEncoded)
 */
class RedStorClient extends Client
{
    public function __construct($parameters = null, $options = null)
    {
        Factory::define(RedStorProfile::class, RedStorProfile::class);
        if (!$options) {
            $options = [];
        }
        $options = array_merge($options, [
            'profile' => RedStorProfile::class,
        ]);
        parent::__construct($parameters, $options);
    }

    public function rsCreateModel(Model $model)
    {
        return $model->create($this);
    }

    public function rsDescribeModel(string $name): Model
    {
        return (new Model())->loadByName($this, $name);
    }
}
