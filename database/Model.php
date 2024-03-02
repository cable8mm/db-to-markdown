<?php

namespace Cable8mm\Database;

use Cable8mm\ImportFromDbToJekyll\DB;
use Cable8mm\ImportFromDbToJekyll\Mappers\Mapper;
use InvalidArgumentException;
use Medoo\Medoo;

class Model
{
    private Medoo $connection;

    private string $table;

    public function __construct()
    {
        $this->connection = new Medoo([
            'type' => 'sqlite',
            'database' => __DIR__.'/database.sqlite',
        ]);

        $this->table = DB::table();
    }

    public function up(): ?\PDOStatement
    {
        $map = Mapper::getMap('DogStory');

        $fields = (new Mapper(...$map))->fields();

        $schema = [];

        foreach ($fields as $item) {
            $schema = array_merge($schema, $this->getField($item, ...$map));
        }

        $schema = array_merge($schema, ['PRIMARY KEY (<id>)']);

        return $this->connection->create($this->table, $schema);
    }

    public function down(): ?\PDOStatement
    {
        return $this->connection->drop($this->table);
    }

    public function getField(string $field, array|string $title, string $permalink, string $body, string $published_at): array
    {
        if ($field === 'id') {
            return ['id' => [
                'INT',
                'NOT NULL',
            ]];
        }

        $description = [];

        if (is_string($title) || (is_array($title) && in_array($field, $title))) {
            $description = [
                'VARCHAR(255)',
            ];
        }

        if ($field === $permalink) {
            $description = [
                'VARCHAR(255)',
            ];
        }

        if ($field === $body) {
            $description = [
                'TEXT',
            ];
        }

        if ($field === $published_at) {
            $description = [
                'TIMESTAMP',
            ];
        }

        if (empty($description)) {
            throw new InvalidArgumentException($field.' and invalid field name not to match mapper fields.');
        }

        return [$field => $description];
    }
}
