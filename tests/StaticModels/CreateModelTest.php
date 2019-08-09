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
    public function testCreate()
    {
        $model = Entities\Model::Factory('testModel')
            ->addColumn(Entities\Column::Factory('id', Types\KeyType::class))
            ->addColumn(Entities\Column::Factory('string', Types\StringType::class))
            ->addColumn(Entities\Column::Factory('decimal', Types\DecimalType::class))
            ->addColumn(Entities\Column::Factory('int', Types\IntType::class))
        ;

        $this->assertEquals(true, $this->redis->rsCreateModel($model));

        $this->assertEquals($model, $this->redis->rsDescribeModel('testModel'));
    }
}
