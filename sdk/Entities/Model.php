<?php

namespace RedStor\SDK\Entities;

use Predis\Client as PredisClient;
use RedStor\RedStor;
use RedStor\SDK\Exceptions;

class Model implements EntityInterface
{
    /** @var string */
    protected $name;
    /** @var Column[] */
    protected $columns;

    public function __construct(string $name = null)
    {
        if ($name) {
            $this->setName($name);
        }
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
            $this->addColumn((new Column())->loadByName($redis, $modelName, $column));
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
        $this->columns[] = $column;

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
}
