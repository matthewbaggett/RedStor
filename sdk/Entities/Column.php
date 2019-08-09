<?php
namespace RedStor\SDK\Entities;

use RedStor\SDK\Types\TypeInterface;

class Column
    implements EntityInterface
{
    /** @var string */
    protected $name;
    /** @var TypeInterface */
    protected $type;

    public static function Factory(string $name = null, string $type = null) : Column {
        new Column($name, $type);
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

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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