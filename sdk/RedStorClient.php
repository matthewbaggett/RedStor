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
 * @method int   zadd($key, int $score, $member)
 * @method bool  auth($password)
 */
class RedStorClient extends Client
{
    private $cachedParameters;
    private $cachedOptions;

    public function __construct($parameters = null, $options = null)
    {
        $this->cachedParameters = $parameters;
        $this->cachedOptions = $options;
        Factory::define(RedStorProfile::class, RedStorProfile::class);
        if (!$options) {
            $options = [];
        }
        $options = array_merge($options, [
            'profile' => RedStorProfile::class,
        ]);
        parent::__construct($parameters, $options);
    }

    public function login($app, $username, $password)
    {
        $authRequest = sprintf('%s:%s:%s', $app, $username, $password);
        \Kint::dump($authRequest);
        $authResponse = $this->auth($authRequest);

        \Kint::dump($authResponse);
        exit;
    }

    public function getPredis(): Client
    {
        return new Client($this->cachedParameters, $this->cachedOptions);
    }

    public function rsCreateModel(Model $model)
    {
        return $model->create($this);
    }

    public function rsDescribeModel(string $name): Model
    {
        return (new Model())->loadByName($this, $name);
    }

    public function hgetall($key)
    {
        $hKeys = $this->hkeys($key);
        $hVals = $this->hmget($key, $hKeys);

        return array_combine($hKeys, $hVals);
    }
}
