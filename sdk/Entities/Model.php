<?php
namespace RedStor\SDK\Entities;

class Model
    implements EntityInterface
{
    /** @var string */
    protected $name;
    /** @var Column[] */
    protected $columns;

    public static function Factory(string $name = null) : Model {
        new Model($name);
    }

    public function __construct(string $name = null)
    {
        if($name) {
            $this->setName($name);
        }
    }

    public function addColumn(Column $column){
        $this->columns = $column;
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

    /**
     * @param string $name
     * @return Model
     */
    public function setName(string $name): Model
    {
        $this->name = $name;
        return $this;
    }



}