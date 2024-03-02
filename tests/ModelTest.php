<?php

namespace Cable8mm\DbToMarkdown\Test;

use Cable8mm\Database\Model;
use PDOStatement;
use PHPUnit\Framework\TestCase;

final class ModelTest extends TestCase
{
    public function test_can_table_make()
    {
        $pdo = Model::getInstance()->up();

        $this->assertInstanceOf(PDOStatement::class, $pdo);
    }

    public function test_can_table_drop()
    {
        $pdo = Model::getInstance()->down();

        $this->assertInstanceOf(PDOStatement::class, $pdo);
    }
}
