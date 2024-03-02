<?php

namespace Cable8mm\DbToMarkdown\Test;

use Cable8mm\Database\Model;
use Cable8mm\Database\Seeder;
use PHPUnit\Framework\TestCase;

final class SeederTest extends TestCase
{
    private Model $model;

    protected function setUp(): void
    {
        $this->model = Model::getInstance();

        $this->model->up();
    }

    protected function tearDown(): void
    {
        $this->model->down();
    }

    public function test_can_seder_work(): void
    {
        $connection = $this->model->getConnection();

        $table = $this->model->getTable();

        $count = $connection->count($table);

        (new Seeder())->run();

        $nextCount = $connection->count($table);

        $this->assertEquals($count + 1, $nextCount);
    }
}
