<?php

namespace Cable8mm\DbToMarkdown\Test;

use Cable8mm\DbToMarkdown\DB;
use PHPUnit\Framework\TestCase;

final class DBTest extends TestCase
{
    public function test_can_connect_db_connection(): void
    {
        $db = DB::getInstance();

        if ($db->isConnected()) {
            $connection = $db->getConnection();

            $this->assertNotNull($connection);
        }

        $this->assertTrue(true);
    }

    public function test_can_get_table_from_env(): void
    {
        $table = DB::table();

        $this->assertIsString($table);
    }

    public function test_can_get_mapper_path(): void
    {
        $mapper = DB::mapper();

        $this->assertIsString($mapper);
        $this->assertStringEndsWith('db-to-markdown.json', $mapper);
    }

    public function test_can_get_author_from_env(): void
    {
        $author = DB::author();

        $this->assertIsString($author);
    }

    public function test_can_get_categories_from_env(): void
    {
        $categories = DB::categories();

        $this->assertIsArray($categories);
        $this->assertArrayHasKey(0, $categories);
    }

    public function test_can_set_custom_instance(): void
    {
        $original = DB::getInstance();

        DB::setInstance(null);

        $newInstance = DB::getInstance();

        $this->assertNotNull($newInstance);

        DB::setInstance($original);
    }

    public function test_get_instance_returns_same_instance(): void
    {
        $instance1 = DB::getInstance();
        $instance2 = DB::getInstance();

        $this->assertSame($instance1, $instance2);
    }
}
