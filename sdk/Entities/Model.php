<?php

namespace RedStor\SDK\Entities;

use Predis\Client as PredisClient;
use RedStor\RedStor;
use RedStor\SDK\Exceptions;
use RedStor\SDK\Types\BoolType;
use RedStor\SDK\Types\DateType;
use RedStor\SDK\Types\KeyType;

class Model implements EntityInterface
{
    public const ORDER_DESC = 'desc';
    public const ORDER_ASC = 'asc';
    /** @var string */
    protected $name;
    /** @var Column[] */
    protected $columns;
    /** @var array */
    protected $data;

    public function __construct(string $name = null)
    {
        if ($name) {
            $this->setName($name);
        }
    }

    public function __call($methodName, $arguments)
    {
        $field = substr($methodName, 3);
        switch (substr($methodName, 0, 3)) {
            case 'set':
                return $this->__set($field, $arguments);
            case 'get':
                return $this->__get($field);
        }

        throw new Exceptions\PropertyDoesntExistException(sprintf(
            "Model %s doesn't have a method called %s.",
            get_called_class(),
            $methodName
        ));
    }

    public function __set($columnName, $arguments): self
    {
        $value = $arguments[0];

        if (!isset($this->columns[self::sanitise($columnName)])) {
            throw new Exceptions\PropertyDoesntExistException(sprintf(
                "Column \"%s\" (%s) doesn't seem to have a definition set. Something bad has happened.",
                $columnName,
                self::sanitise($columnName)
            ));
        }

        $type = $this->columns[self::sanitise($columnName)]->getType();

        if (!$type->validate($value)) {
            throw new Exceptions\ColumnDataDoesntMatchColumnType(sprintf(
                "Column \"%s\" type \"%s\" doesn't accept \"%s\" as a value.",
                $columnName,
                get_class($this->columns[self::sanitise($columnName)]),
                $value
            ));
        }

        $this->data[self::sanitise($columnName)] = $value;

        return $this;
    }

    public function __get($name)
    {
        return $this->data[self::sanitise($name)];
    }

    public function __getNextId(PredisClient $redis): int
    {
        $key = [];

        foreach ($this->getColumns() as $column) {
            if ($column->getType() instanceof KeyType) {
                if (!isset($this->data[$column->getName_clean()])) {
                    $indexKey = sprintf(
                        RedStor::KEY_MODEL_INDEX,
                        $this->getName_clean(),
                        $column->getName_clean()
                    );
                    $count = $redis->zcount($indexKey, '-inf', '+inf');
                    $key[] = $count + 1;
                }
            }
        }

        return count($key) ? implode(':', $key) : null;
    }

    public function __getFirstKey(): ?Column
    {
        foreach ($this->getColumns() as $column) {
            if ($column->getType() instanceof KeyType) {
                return $column;
            }
        }

        return null;
    }

    public function __getId()
    {
        $key = [];
        foreach ($this->getColumns() as $column) {
            if ($column->getType() instanceof KeyType) {
                if (isset($this->data[$column->getName_clean()])) {
                    $key[] = $this->data[$column->getName_clean()];
                }
            }
        }

        return count($key) ? implode(':', $key) : null;
    }

    public function __setId($newId): self
    {
        foreach ($this->getColumns() as $column) {
            if ($column->getType() instanceof KeyType) {
                if (!isset($this->data[$column->getName_clean()])) {
                    $this->data[$column->getName_clean()] = $newId;
                }
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     *
     * @return Model
     */
    public function setData(array $data): Model
    {
        $this->data = $data;

        return $this;
    }

    public function newItem(): Model
    {
        return (new self())
            ->setName($this->getName())
            ->setColumns($this->getColumns())
        ;
    }

    public function save(PredisClient $redis): self
    {
        $id = $this->__getId() ?? $this->__getNextId($redis);
        $this->__setId($id);
        $dict = [];
        $storageKey = sprintf(
            RedStor::KEY_MODEL_ITEM,
            $this->getName_clean(),
            $id
        );
        $flushToDbQueueKey = sprintf(
            RedStor::KEY_FLUSH_QUEUE
        );
        foreach ($this->getColumns() as $column) {
            $value = $this->data[$column->getName_clean()];

            switch (get_class($column->getType())) {
                case DateType::class:
                    /** @var \DateTime $value */
                    $value = $value->format('Y-m-d H:i:s');

                    break;
                case BoolType::class:
                    $value = $value ? 'True' : 'False';

                    break;
            }

            if ($column->getType()->isPrimaryKey()) {
                $indexKey = sprintf(
                    RedStor::KEY_MODEL_INDEX,
                    $this->getName_clean(),
                    $column->getName_clean()
                );
                $itemKey = sprintf(
                    RedStor::KEY_MODEL_ITEM,
                    $this->getName_clean(),
                    $this->__getId()
                );
                $redis->zadd($indexKey, $this->__getId(), $itemKey);
            }

            $dict[$column->getName_clean()] = $value;
        }

        #\Kint::dump($storageKey, $dict);

        $redis->hmset($storageKey, $dict);

        $redis->sadd($flushToDbQueueKey, [$storageKey]);

        $countNumberRecieved = $redis->publish(RedStor::CHANNEL_QUEUE_FLUSHTODB, $redis->scard($flushToDbQueueKey));
        if($countNumberRecieved == 0){
            // If a tree falls in a forest, and nobody hears it?
        }

        return $this;
    }

    /**
     * @param mixed $direction
     *
     * @return Model[]
     */
    public function select(PredisClient $redis, string $orderBy = null, $direction = self::ORDER_ASC): array
    {
        $results = [];

        $indexKey = sprintf(
            RedStor::KEY_MODEL_INDEX,
            $this->getName_clean(),
            $orderBy ?? $this->__getFirstKey()->getName_clean()
        );

        //\Kint::dump($indexKey);

        $keys = $redis->zrange($indexKey, 0, -1);

        if (self::ORDER_DESC == $direction) {
            $keys = array_reverse($keys);
        }
        foreach ($keys as $key) {
            $results[] = (new Model($this->getName()))
                ->setColumns($this->getColumns())
                ->setData($redis->hgetall($key))
            ;
        }

        return $results;
    }

    public static function Factory(string $name = null): Model
    {
        return new Model($name);
    }

    public function loadByName(PredisClient $redis, string $modelName): Model
    {
        if (!$redis->sismember(RedStor::KEY_MODEL_LIST_SET, $modelName)) {
            throw new Exceptions\ModelDoesntExistException(sprintf("Model %s doesn't exist!", $modelName));
        }

        $this->setName($modelName);

        $columns = $redis->zrange(
            sprintf(RedStor::KEY_MODEL_COLUMN_LIST_SET, $this->getName()),
            0,
            -1
        );

        foreach ($columns as $column) {
            $this->addColumn(
                (new Column())
                    ->loadByName($redis, $modelName, $column)
            );
        }

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->getName(),
            'columns' => $this->getColumns(),
        ];
    }

    public function addColumn(Column $column)
    {
        $this->columns[$column->getName_clean()] = $column;

        return $this;
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param Column[] $columns
     *
     * @return Model
     */
    public function setColumns(array $columns): Model
    {
        $this->columns = $columns;

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
     * @return Model
     */
    public function setName(string $name): Model
    {
        $this->name = $name;

        return $this;
    }

    public function create(PredisClient $redis)
    {
        $redis->modelCreate($this->getName_clean());
        foreach ($this->getColumns() as $column) {
            $column->create($redis, $this);
        }

        return true;
    }

    public function delete(PredisClient $redis)
    {
        $redis->modelDelete($this->getName_clean());
    }

    protected static function sanitise($string): string
    {
        // @todo ugh
        return lcfirst($string);
    }
}
