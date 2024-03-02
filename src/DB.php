<?php

namespace Cable8mm\DbToMarkdown;

use Dotenv\Dotenv;
use Medoo\Medoo;

class DB
{
    private static $instance = null;

    private ?Medoo $connection = null;

    public function __construct()
    {
        self::loadEnv();

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

        if (! empty($_ENV['DB_DATABASE'])) {
            $this->connection = new Medoo($params);
        }
    }

    private static function loadEnv(): void
    {
        $dotenv = Dotenv::createImmutable(getcwd());
        $dotenv->load();
    }

    public static function getInstance(): static
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function isConnected(): bool
    {
        return ! is_null($this->connection);
    }

    public function getConnection(): Medoo
    {
        return $this->connection;
    }

    public static function table(): string
    {
        if (! isset($_ENV['DB_TABLE'])) {
            self::loadEnv();
        }

        return $_ENV['DB_TABLE'];
    }
}
