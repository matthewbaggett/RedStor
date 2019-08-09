<?php
namespace RedStor\SDK\Entities;

use Predis\Pipeline\Pipeline;

class Model
    implements EntityInterface
{
    const KEY_MODEL_LIST_SET = "RedStor:Models";

    /** @var string */
    protected $name;
    /** @var Column[] */
    protected $columns;

    public static function Factory(string $name = null) : Model {
        return new Model($name);
    }

    public function __construct(string $name = null)
    {
        if($name) {
            $this->setName($name);
        }
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->getName(),
            'columns' => $this->getColumns(),
        ];
    }

    public function addColumn(Column $column){
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

    public function getName_clean() : string {
        $name = $this->getName();
        $name = preg_replace("/[^A-Za-z0-9 ]/", '', $name);
        $name = strtolower($name);
        $name = str_replace(" ", "-", $name);
        return $name;
    }

    /**
     * @param string $name
     * @return Model
     */
    public function setName(string $name): Model
    {
        $this->name = $name;
        return $this;
    }

    public function create(Pipeline $pipeline): Pipeline
    {
        $pipeline->sadd(self::KEY_MODEL_LIST_SET, [$this->getName_clean()]);
        foreach($this->getColumns() as $column){
            $pipeline = $column->create($pipeline, $this);
        }
        return $pipeline;
    }

    public function delete(Pipeline $pipeline): Pipeline
    {
        $pipeline->srem(self::KEY_MODEL_LIST_SET, $this->getName_clean());
        foreach($this->getColumns() as $column){
            $pipeline = $column->delete($pipeline, $this);
        }
        return $pipeline;
    }


}