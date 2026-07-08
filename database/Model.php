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

    private string $currentMapper;

    private function __construct()
    {
        $databasePath = defined('DB_TEST_DATABASE') ? DB_TEST_DATABASE : (__DIR__.'/../database/database.sqlite');

        $this->connection = new Medoo([
            'type' => 'sqlite',
            'database' => $databasePath,
        ]);

        $this->table = DB::table();

        $this->currentMapper = DB::mapper();

        $this->faker = Factory::create();
    }

    public static function getInstance(): static
    {
        if (self::$instance === null) {
            self::$instance = new self;
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
        $map = Mapper::getMap($this->currentMapper);

        $mapper = new Mapper(...$map);

        $fields = $mapper->fields();

        $schema = [];

        foreach ($fields as $item) {
            $schema = array_merge($schema, $this->getField($item, $mapper));
        }

        return $this->connection->create($this->table, $schema);
    }

    public function down(): ?\PDOStatement
    {
        return $this->connection->drop($this->table);
    }

    public function factory(): ?\PDOStatement
    {
        $map = Mapper::getMap($this->currentMapper);

        $mapper = new Mapper(...$map);

        $fields = $mapper->fields();

        $factory = [];

        foreach ($fields as $item) {
            $fake = $this->getFake($item, $mapper);

            if (! $fake) {
                continue;
            }

            $factory = array_merge($factory, $fake);
        }

        return $this->connection->insert($this->table, $factory);
    }

    public function getField(string $field, Mapper $mapper): array
    {
        if ($field === 'id') {
            return ['id' => [
                'INTEGER',
                'NOT NULL',
                'PRIMARY KEY',
            ]];
        }

        $description = [];

        $title = $mapper->getTitle();
        $slug = $mapper->getSlug();
        $categories = $mapper->getCategories();
        $body = $mapper->getBody();
        $publishedAt = $mapper->getPublishedAt();

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

        if ($field === $publishedAt) {
            $description = [
                'DATETIME',
            ];
        }

        if (empty($description)) {
            throw new InvalidArgumentException($field.' and invalid field name not to match mapper fields.');
        }

        return [$field => $description];
    }

    public function getFake(string $field, Mapper $mapper): array|bool
    {
        if ($field === 'id') {
            return false;
        }

        $title = $mapper->getTitle();
        $slug = $mapper->getSlug();
        $categories = $mapper->getCategories();
        $body = $mapper->getBody();
        $publishedAt = $mapper->getPublishedAt();

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

        if ($field === $publishedAt) {
            $fake = $this->faker->dateTime()->format('Y-m-d H:i:s');
        }

        if (empty($fake)) {
            throw new InvalidArgumentException($field.' for fake and invalid field name not to match mapper fields.');
        }

        return [$field => $fake];
    }
}
