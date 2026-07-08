<?php

namespace Cable8mm\DbToMarkdown\Test;

use Cable8mm\Database\Model;
use Medoo\Medoo;
use PDOStatement;
use PHPUnit\Framework\TestCase;

final class ModelTest extends TestCase
{
    public function test_can_table_make(): void
    {
        $pdo = Model::getInstance()->up();

        $this->assertInstanceOf(PDOStatement::class, $pdo);
    }

    public function test_can_table_drop(): void
    {
        $pdo = Model::getInstance()->down();

        $this->assertInstanceOf(PDOStatement::class, $pdo);
    }

    public function test_get_connection_returns_medoo_instance(): void
    {
        $connection = Model::getInstance()->getConnection();

        $this->assertInstanceOf(Medoo::class, $connection);
    }

    public function test_get_table_returns_string(): void
    {
        $table = Model::getInstance()->getTable();

        $this->assertIsString($table);
    }

    public function test_get_instance_is_singleton(): void
    {
        $instance1 = Model::getInstance();
        $instance2 = Model::getInstance();

        $this->assertSame($instance1, $instance2);
    }

    public function test_up_creates_table_down_drops_table(): void
    {
        $model = Model::getInstance();

        $model->up();

        $connection = $model->getConnection();
        $table = $model->getTable();

        $count = $connection->count($table);

        $this->assertEquals(0, $count);

        $model->down();
    }

    public function test_factory_inserts_record(): void
    {
        $model = Model::getInstance();

        $model->down();
        $model->up();

        $connection = $model->getConnection();
        $table = $model->getTable();

        $countBefore = $connection->count($table);

        $model->factory();

        $countAfter = $connection->count($table);

        $this->assertEquals($countBefore + 10, $countAfter);

        $model->down();
    }
}
