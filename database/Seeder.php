<?php

namespace Cable8mm\Database;

class Seeder
{
    private Model $model;

    public function __construct()
    {
        $this->model = Model::getInstance();
    }

    public function run(): void
    {
        $this->model->factory();
    }
}
