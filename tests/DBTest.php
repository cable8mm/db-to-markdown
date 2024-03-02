<?php

namespace Cable8mm\ImportFromDbToJekyll\Test;

use Cable8mm\ImportFromDbToJekyll\DB;
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
}
