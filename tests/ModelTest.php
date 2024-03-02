<?php

namespace Cable8mm\ImportFromDbToJekyll\Test;

use Cable8mm\Database\Model;
use PDOStatement;
use PHPUnit\Framework\TestCase;

final class ModelTest extends TestCase
{
    public function test_can_table_make()
    {
        $pdo = (new Model())->up();

        $this->assertInstanceOf(PDOStatement::class, $pdo);
    }

    public function test_can_table_drop()
    {
        $pdo = (new Model())->down();

        $this->assertInstanceOf(PDOStatement::class, $pdo);
    }
}
