<?php
namespace Cable8mm\ImportFromDbToJekyll;

use Dotenv\Dotenv;
use Medoo\Medoo;

class DB {
    private static $instance = null;

    private Medoo $connection;

    private function __construct()
    {
        $dotenv = Dotenv::createImmutable(getcwd());
        $dotenv->load();

        $params = [
            'type' => $_ENV['DB_CONNECTION'],
            'host' => $_ENV['DB_HOST'],
            'database' => $_ENV['DB_DATABASE'],
            'username' => $_ENV['DB_USERNAME'],
            'password' => $_ENV['DB_PASSWORD'],
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'port' => $_ENV['DB_PORT'],
        ];

        $this->connection = new Medoo($params);
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
}
