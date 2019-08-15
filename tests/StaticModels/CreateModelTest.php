<?php

namespace RedStor\Tests\Redis;

use RedStor\SDK\Entities;
use RedStor\SDK\Types;
use RedStor\Tests\RedStorTest;
use ⌬\Tests\Traits\FakeDataTrait;

/**
 * @internal
 * @coversNothing
 */
class CreateModelTest extends RedStorTest
{
    use FakeDataTrait;

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

    public function testCreateABlog()
    {
        // Wipe the database.
        $this->redis->flushall();

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

        $this->assertEquals(true, $this->redis->rsCreateModel($users));
        $this->assertEquals(true, $this->redis->rsCreateModel($blogPosts));
        $this->assertEquals(true, $this->redis->rsCreateModel($comments));
        $this->assertEquals($users, $this->redis->rsDescribeModel('users'));
        $this->assertEquals($blogPosts, $this->redis->rsDescribeModel('posts'));
        $this->assertEquals($comments, $this->redis->rsDescribeModel('comments'));

        $userName = $this->faker()->userName;
        $email = $this->faker()->safeEmail;
        $password = $this->faker()->password;
        $created = $this->faker()->dateTime;
        $active = $this->faker()->boolean;

        \Kint::dump($userName, $email, $password, $created, $active);

        /** @var Entities\Model $newUser */
        $newUser = $users->newItem()
            ->setUsername($userName)
            ->setEmail($email)
            ->setPassword(password_hash($password, PASSWORD_DEFAULT))
            ->setCreated($created)
            ->setActive($active);

        $this->assertInstanceOf(Entities\Model::class, $newUser);
        $this->assertEquals($userName, $newUser->getUsername());
        $this->assertEquals($email, $newUser->getEmail());
        $this->assertTrue(password_verify($password, $newUser->getPassword()));
        $this->assertEquals($created, $newUser->getCreated());
        $this->assertEquals($active, $newUser->getActive());

        $newUser->save($this->redis);

        for($i = 0; $i <= $this->faker()->numberBetween(3,10); $i++) {
            $blogPostObject = ($blogPosts->newItem())
                ->setTitle($this->faker()->words(5, true))
                ->setPost($this->faker()->words(50, true))
                ->setCreated($this->faker()->dateTime)
                ->setPublished($this->faker()->dateTime)
                ->setUserId($newUser->getUserId())
                ->save($this->redis);

            for($j = 0; $j <= $this->faker()->numberBetween(2,5); $j++){
                ($comments->newItem())
                    ->setPostId($blogPostObject->getPostId())
                    ->setUserId($newUser->getUserId())
                    ->setComment($this->faker()->words(50, true))
                    ->setCreated($this->faker()->dateTime)
                    ->save($this->redis);
            }
        }
    }
}
