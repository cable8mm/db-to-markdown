<?php

namespace Cable8mm\ImportFromDbToJekyll\Test;

use Cable8mm\ImportFromDbToJekyll\DB;
use PHPUnit\Framework\TestCase;

final class DBTest extends TestCase
{
    public function test_can_connect_db_connection(): void
    {
        $connection = DB::getInstance()->getConnection();

        $this->assertNotNull($connection);
    }

    public function test_can_get_table_from_env(): void
    {
        $table = DB::table();

        $this->assertIsString($table);
    }
}
