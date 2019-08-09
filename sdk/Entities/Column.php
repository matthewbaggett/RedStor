<?php
namespace RedStor\SDK\Entities;

use Predis\Pipeline\Pipeline;
use RedStor\SDK\Types\TypeInterface;

class Column
    implements EntityInterface
{
    const KEY_MODEL_COLUMN_DEFINITION = "RedStor:Models:%s:Columns";
    /** @var string */
    protected $name;
    /** @var TypeInterface */
    protected $type;

    public static function Factory(string $name = null, string $type = null) : Column {
        return new Column($name, $type);
    }

    public function __construct(string $name = null, string $type = null)
    {
        if($name) {
            $this->setName($name);
        }
        if($type) {
            $this->setType(new $type);
        }
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->getName(),
            'type' => $this->getType(),
        ];
    }

    public function create(Pipeline $pipeline, Model $model = null): Pipeline
    {
        $pipeline->hset(
            sprintf(self::KEY_MODEL_COLUMN_DEFINITION, $model->getName_clean()),
            $this->getName(),
            json_encode($this)
        );

        return $pipeline;
    }

    public function delete(Pipeline $pipeline, Model $model = null) : Pipeline
    {
        $pipeline->hdel(
            sprintf(self::KEY_MODEL_COLUMN_DEFINITION, $model->getName_clean()),
            [$this->getName_clean()]
        );
        return $pipeline;
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
     * @return Column
     */
    public function setType(TypeInterface $type): Column
    {
        $this->type = $type;
        return $this;
    }


}