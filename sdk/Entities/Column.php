<?php

namespace RedStor\SDK\Entities;

use Predis\Client as PredisClient;
use RedStor\RedStor;
use RedStor\SDK\Exceptions\ColumnTypeDoesntExistException;
use RedStor\SDK\Types\TypeInterface;

class Column implements EntityInterface
{
    public const KEY_MODEL_COLUMN_DEFINITION = 'RedStor:Models:%s:Columns';
    /** @var string */
    protected $name;
    /** @var TypeInterface */
    protected $type;

    public function __construct(string $name = null, string $type = null)
    {
        if ($name) {
            $this->setName($name);
        }
        if ($type) {
            $this->setType(new $type());
        }
    }

    public static function Factory(string $name = null, string $type = null): Column
    {
        return new Column($name, $type);
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->getName(),
            'type' => $this->getType(),
        ];
    }

    public function create(PredisClient $redis, Model $model = null)
    {
        $redis->modelAddColumn($model->getName_clean(), $this->getName_clean(), $this->getType()->getName());

        return true;
    }

    public function delete(PredisClient $redis, Model $model = null)
    {
        $responses = [];
        $responses[] = $redis->modelDelColumn($model->getName_clean(), $this->getName_clean());

        return $responses;
    }

    public function loadByName(PredisClient $redis, string $modelName, string $columnName): Column
    {
        $this->setName($redis->get(sprintf(RedStor::KEY_MODEL_COLUMN_NAME, $modelName, $columnName)));
        $typeName = $redis->get(sprintf(RedStor::KEY_MODEL_COLUMN_TYPE, $modelName, $columnName));
        $typeClass = "RedStor\\SDK\\Types\\{$typeName}Type";
        if (!class_exists($typeClass)) {
            throw new ColumnTypeDoesntExistException(sprintf("Column type %s doesn't exist.", $typeName));
        }
        $this->setType(new $typeClass());

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getName_clean(): string
    {
        $name = $this->getName();
        $name = preg_replace('/[^A-Za-z0-9 ]/', '', $name);
        //$name = strtolower($name);
        $name = str_replace(' ', '-', $name);

        return trim($name);
    }

    /**
     * @param string $name
     *
     * @return Column
     */
    public function setName(string $name): Column
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return TypeInterface
     */
    public function getType(): TypeInterface
    {
        return $this->type;
    }

    /**
     * @param TypeInterface $type
     *
     * @return Column
     */
    public function setType(TypeInterface $type): Column
    {
        $this->type = $type;

        return $this;
    }
}
