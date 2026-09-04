<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class InitialSchema extends BaseMigration
{
    public bool $autoId = false;

    public function change(): void
    {
        $this->table('users')
            ->addColumn('id', 'integer', ['autoIncrement' => true, 'null' => false])
            ->addColumn('email', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('password', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('name', 'string', ['limit' => 120, 'null' => false])
            ->addColumn('role', 'string', ['limit' => 20, 'null' => false, 'default' => 'friend'])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addPrimaryKey(['id'])
            ->addIndex(['email'], ['unique' => true])
            ->create();

        $this->table('trips')
            ->addColumn('id', 'integer', ['autoIncrement' => true, 'null' => false])
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('title', 'string', ['limit' => 180, 'null' => false])
            ->addColumn('slug', 'string', ['limit' => 200, 'null' => false])
            ->addColumn('summary', 'text', ['null' => true])
            ->addColumn('visibility', 'string', ['limit' => 20, 'null' => false, 'default' => 'friends'])
            ->addColumn('starts_on', 'date', ['null' => true])
            ->addColumn('ends_on', 'date', ['null' => true])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addPrimaryKey(['id'])
            ->addIndex(['slug'], ['unique' => true])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this->table('trip_members')
            ->addColumn('id', 'integer', ['autoIncrement' => true, 'null' => false])
            ->addColumn('trip_id', 'integer', ['null' => false])
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('role', 'string', ['limit' => 20, 'null' => false, 'default' => 'viewer'])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addPrimaryKey(['id'])
            ->addIndex(['trip_id', 'user_id'], ['unique' => true])
            ->addForeignKey('trip_id', 'trips', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this->table('invites')
            ->addColumn('id', 'integer', ['autoIncrement' => true, 'null' => false])
            ->addColumn('trip_id', 'integer', ['null' => false])
            ->addColumn('invited_by_user_id', 'integer', ['null' => false])
            ->addColumn('email', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('token', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('role', 'string', ['limit' => 20, 'null' => false, 'default' => 'editor'])
            ->addColumn('expires_at', 'datetime', ['null' => false])
            ->addColumn('accepted_at', 'datetime', ['null' => true])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addPrimaryKey(['id'])
            ->addIndex(['token'], ['unique' => true])
            ->addForeignKey('trip_id', 'trips', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('invited_by_user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this->table('pins')
            ->addColumn('id', 'integer', ['autoIncrement' => true, 'null' => false])
            ->addColumn('trip_id', 'integer', ['null' => false])
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('title', 'string', ['limit' => 180, 'null' => false])
            ->addColumn('body', 'text', ['null' => true])
            ->addColumn('place_name', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('latitude', 'decimal', ['precision' => 10, 'scale' => 7, 'null' => false])
            ->addColumn('longitude', 'decimal', ['precision' => 10, 'scale' => 7, 'null' => false])
            ->addColumn('taken_at', 'datetime', ['null' => true])
            ->addColumn('sort_order', 'integer', ['null' => false, 'default' => 0])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addPrimaryKey(['id'])
            ->addForeignKey('trip_id', 'trips', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $this->table('media_items')
            ->addColumn('id', 'integer', ['autoIncrement' => true, 'null' => false])
            ->addColumn('pin_id', 'integer', ['null' => false])
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('kind', 'string', ['limit' => 20, 'null' => false, 'default' => 'image'])
            ->addColumn('filename', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('original_name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('mime', 'string', ['limit' => 120, 'null' => false])
            ->addColumn('filesize', 'integer', ['null' => false, 'default' => 0])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addPrimaryKey(['id'])
            ->addForeignKey('pin_id', 'pins', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
