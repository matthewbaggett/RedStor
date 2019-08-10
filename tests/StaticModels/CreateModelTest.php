<?php

namespace RedStor\Tests\Redis;

use RedStor\SDK\Entities;
use RedStor\SDK\Types;
use RedStor\Tests\RedStorTest;

/**
 * @internal
 * @coversNothing
 */
class CreateModelTest extends RedStorTest
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
    }

    public function testCreateRaw()
    {
        $this->assertEquals(['OK'], $this->redis->modelCreate('rawTestModel'));
        $this->assertEquals(['OK'], $this->redis->modelAddColumn('rawTestModel', 'id', 'key'));
        $this->assertEquals(['OK'], $this->redis->modelAddColumn('rawTestModel', 'colString', 'string'));
        $this->assertEquals(['OK'], $this->redis->modelAddColumn('rawTestModel', 'colDecimal', 'decimal'));
        $this->assertEquals(['OK'], $this->redis->modelAddColumn('rawTestModel', 'colInt', 'int'));

        //$this->assertEquals(['OK'], $this->redis->modelAddColumns('rawTestModel', [
        //    'min' => 'int',
        //    'max' => 'int',
        //]));
    }

    public function testCreate()
    {
        $model = Entities\Model::Factory('testModel')
            ->addColumn(Entities\Column::Factory('id', Types\KeyType::class))
            ->addColumn(Entities\Column::Factory('colString', Types\StringType::class))
            ->addColumn(Entities\Column::Factory('colDecimal', Types\DecimalType::class))
            ->addColumn(Entities\Column::Factory('colInt', Types\IntType::class))
        ;

        $this->assertEquals(true, $this->redis->rsCreateModel($model));

        $this->assertEquals($model, $this->redis->rsDescribeModel('testModel'));
    }
}
