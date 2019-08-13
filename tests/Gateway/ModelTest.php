<?php

namespace RedStor\Tests\Gateway;

use GuzzleHttp\RequestOptions;
use RedStor\SDK\Entities;
use RedStor\SDK\Types;

/**
 * @internal
 * @covers \RedStor\Controllers\ModelController
 */
class ModelTest extends GatewayTest
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
    }

    public function dataProviderModels()
    {
        $users = Entities\Model::Factory('users')
            ->addColumn(Entities\Column::Factory('userId', Types\KeyType::class))
            ->addColumn(Entities\Column::Factory('username', Types\StringType::class))
            ->addColumn(Entities\Column::Factory('email', Types\EmailType::class))
            ->addColumn(Entities\Column::Factory('password', Types\PasswordType::class))
            ->addColumn(Entities\Column::Factory('created', Types\DateType::class))
            ->addColumn(Entities\Column::Factory('active', Types\BoolType::class))
        ;

        $blogPosts = Entities\Model::Factory('posts')
            ->addColumn(Entities\Column::Factory('postId', Types\KeyType::class))
            ->addColumn(Entities\Column::Factory('userId', Types\ForeignKeyType::class, ['relatedTo' => 'users.userId']))
            ->addColumn(Entities\Column::Factory('title', Types\TextType::class))
            ->addColumn(Entities\Column::Factory('post', Types\TextType::class))
            ->addColumn(Entities\Column::Factory('created', Types\DateType::class))
            ->addColumn(Entities\Column::Factory('published', Types\DateType::class))
        ;

        $comments = Entities\Model::Factory('comments')
            ->addColumn(Entities\Column::Factory('commentId', Types\KeyType::class))
            ->addColumn(Entities\Column::Factory('postId', Types\ForeignKeyType::class, ['relatedTo' => 'posts.postId']))
            ->addColumn(Entities\Column::Factory('userId', Types\ForeignKeyType::class, ['relatedTo' => 'users.userId']))
            ->addColumn(Entities\Column::Factory('comment', Types\TextType::class))
            ->addColumn(Entities\Column::Factory('created', Types\DateType::class))
        ;

        return [
            [$users],
            [$blogPosts],
            [$comments],
        ];
    }

    /**
     * @dataProvider dataProviderModels
     */
    public function testCreateRaw(Entities\Model $model)
    {
        $response = $this->guzzle->put(
            "/v1/model/{$model->getName_clean()}",
            [
                RequestOptions::JSON => json_encode($model),
            ]
        );

        $json = json_decode($response->getBody()->getContents(), true);

        \Kint::dump($json);

        $this->assertArrayHasKey('Status', $json);

        $this->assertEquals('Okay', $json['Status']);
    }
}
