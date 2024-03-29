<?php

namespace Cable8mm\Database;

use Cable8mm\DbToMarkdown\DB;
use Cable8mm\DbToMarkdown\Mappers\Mapper;
use Faker\Factory;
use Faker\Generator;
use InvalidArgumentException;
use Medoo\Medoo;

class Model
{
    private static $instance = null;

    private Medoo $connection;

    private string $table;

    private Generator $faker;

    private function __construct()
    {
        $this->connection = new Medoo([
            'type' => 'sqlite',
            'database' => __DIR__.'/../database/database.sqlite',
        ]);

        $this->table = DB::table();

        $this->faker = Factory::create();
    }

    public static function getInstance(): static
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection(): Medoo
    {
        return $this->connection;
    }

    public function getTable(): string
    {
        return DB::table();
    }

    public function up(): ?\PDOStatement
    {
        $map = Mapper::getMap('DogStory');

        $fields = (new Mapper(...$map))->fields();

        $schema = [];

        foreach ($fields as $item) {
            $schema = array_merge($schema, $this->getField($item, ...$map));
        }

        return $this->connection->create($this->table, $schema);
    }

    public function down(): ?\PDOStatement
    {
        return $this->connection->drop($this->table);
    }

    public function factory(): ?\PDOStatement
    {
        $map = Mapper::getMap('DogStory');

        $fields = (new Mapper(...$map))->fields();

        $factory = [];

        foreach ($fields as $item) {
            $fake = $this->getFake($item, ...$map);

            if (! $fake) {
                continue;
            }

            $factory = array_merge($factory, $fake);
        }

        return $this->connection->insert($this->table, $factory);
    }

    public function getField(string $field, array|string $title, string $slug, string $categories, string $body, string $published_at): array
    {
        if ($field === 'id') {
            return ['id' => [
                'INTEGER',
                'NOT NULL',
                'PRIMARY KEY',
            ]];
        }

        $description = [];

        if (is_string($title) || (is_array($title) && in_array($field, $title))) {
            $description = [
                'VARCHAR',
            ];
        }

        if ($field === $slug) {
            $description = [
                'VARCHAR',
            ];
        }

        if ($field === $categories) {
            $description = [
                'VARCHAR',
            ];
        }

        if ($field === $body) {
            $description = [
                'TEXT',
            ];
        }

        if ($field === $published_at) {
            $description = [
                'DATETIME',
            ];
        }

        if (empty($description)) {
            throw new InvalidArgumentException($field.' and invalid field name not to match mapper fields.');
        }

        return [$field => $description];
    }

    public function getFake(string $field, array|string $title, string $slug, string $categories, string $body, string $published_at): array|bool
    {
        if ($field === 'id') {
            return false;
        }

        $description = [];

        if (is_string($title) || (is_array($title) && in_array($field, $title))) {
            $fake = $this->faker->sentence();
        }

        if ($field === $slug) {
            $fake = $this->faker->word();
        }

        if ($field === $categories) {
            $fake = $this->faker->word();
        }

        if ($field === $body) {
            $fake = $this->faker->text();
        }

        if ($field === $published_at) {
            $fake = $this->faker->dateTime()->format('Y-m-d H:i:s');
        }

        if (empty($fake)) {
            throw new InvalidArgumentException($field.' for fake and invalid field name not to match mapper fields.');
        }

        return [$field => $fake];
    }
}
