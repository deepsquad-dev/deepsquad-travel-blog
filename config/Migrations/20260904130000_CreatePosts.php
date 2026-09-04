<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreatePosts extends BaseMigration
{
    public bool $autoId = false;

    public function change(): void
    {
        $this->table('posts')
            ->addColumn('id', 'integer', ['autoIncrement' => true, 'null' => false])
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('trip_id', 'integer', ['null' => true, 'default' => null])
            ->addColumn('title', 'string', ['limit' => 200, 'null' => false])
            ->addColumn('slug', 'string', ['limit' => 220, 'null' => false])
            ->addColumn('body', 'text', ['null' => false])
            ->addColumn('tags', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('status', 'string', ['limit' => 20, 'null' => false, 'default' => 'draft'])
            ->addColumn('visibility', 'string', ['limit' => 20, 'null' => false, 'default' => 'friends'])
            ->addColumn('published_at', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addPrimaryKey(['id'])
            ->addIndex(['slug'], ['unique' => true])
            ->addIndex(['status', 'visibility', 'published_at'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('trip_id', 'trips', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE'])
            ->create();
    }
}
