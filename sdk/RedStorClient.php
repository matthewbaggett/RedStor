<?php

namespace RedStor\SDK;

use Predis\Client;
use Predis\Profile\Factory;
use Predis\Response\Status;
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
        $defaultOptions = [
            'timeout' => 2.0,
            'read_write_timeout' => 2.0,
            'profile' => RedStorProfile::class,
        ];
        Factory::define(RedStorProfile::class, RedStorProfile::class);
        if ($options) {
            $options = array_merge($defaultOptions, $options);
        } else {
            $options = $defaultOptions;
        }

        $this->cachedParameters = $parameters;
        $this->cachedOptions = $options;

        parent::__construct($parameters, $options);
    }

    public function login($app, $username, $password): bool
    {
        $authRequest = sprintf('%s:%s:%s', $app, $username, $password);
        /** @var Status $authResponse */
        $authResponse = $this->auth($authRequest);

        return "AUTH Hello {$app}/{$username} !" == $authResponse->getPayload();
    }

    public function ping($message = null)
    {
        @list($pong, $replyMessage) = parent::ping($message);
        //\Kint::dump($pong, $replyMessage);
        if (!$replyMessage) {
            return true;
        }

        return $replyMessage;
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
