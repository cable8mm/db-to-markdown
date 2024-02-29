<?php

namespace Cable8mm\ImportFromDbToJekyll\Test;

use Cable8mm\ImportFromDbToJekyll\DB;
use PHPUnit\Framework\TestCase;

final class DBTest extends TestCase
{
    public function test_db_connection()
    {
        $connection = DB::getInstance()->getConnection();

        $this->assertNotNull($connection);
    }
}
