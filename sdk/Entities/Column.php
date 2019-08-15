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
    /** @var array */
    protected $options = [];

    public function __construct(string $name = null, string $type = null, array $options = null)
    {
        if ($name) {
            $this->setName($name);
        }
        if ($type) {
            $this->setType(new $type());
        }
        if ($options) {
            $this->setOptions($options);
        }
    }

    public static function Factory(string $name = null, string $type = null, array $options = null): Column
    {
        return new Column($name, $type, $options);
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->getName(),
            'type' => $this->getType(),
            'options' => $this->getOptions(),
        ];
    }

    public function create(PredisClient $redis, Model $model = null)
    {
        //\Kint::dump(
        //    $model->getName_clean(),
        //    $this->getName_clean(),
        //    $this->getType()->getName(),
        //    json_encode($this->getOptions())
        //);
        $redis->modelAddColumn($model->getName_clean(), $this->getName_clean(), $this->getType()->getName(), json_encode($this->getOptions()));

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
        $keyColumnName = sprintf(RedStor::KEY_MODEL_COLUMN_NAME, $modelName, $columnName);
        $keyColumnType = sprintf(RedStor::KEY_MODEL_COLUMN_TYPE, $modelName, $columnName);
        $keyColumnOptions = sprintf(RedStor::KEY_MODEL_COLUMN_OPTIONS, $modelName, $columnName);
        //\Kint::dump(
        //    $modelName,
        //    $columnName,
        //    $keyColumnName,
        //    $keyColumnType,
        //    $keyColumnOptions
        //);
        $this->setName($redis->get($keyColumnName));
        $typeName = $redis->get($keyColumnType);
        //\Kint::dump(
        //    $redis->get($keyColumnName),
        //    $redis->get($keyColumnType)
        //);
        $typeClass = "RedStor\\SDK\\Types\\{$typeName}Type";
        if (!class_exists($typeClass)) {
            throw new ColumnTypeDoesntExistException(sprintf("Column type \"%s\" doesn't exist.", $typeName));
        }
        $this->setType(new $typeClass());

        if($redis->exists($keyColumnOptions)) {
            $this->setOptions($redis->hgetall($keyColumnOptions));
        }

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

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return Column
     */
    public function setOptions(array $options): Column
    {
        $this->options = $options;

        return $this;
    }
}
