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

        $params = [];

        if ($_ENV['DB_CONNECTION'] == 'mysql') {
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
        }

        if ($_ENV['DB_CONNECTION'] == 'sqlite') {
            $params = [
                'type' => 'sqlite',
                'database' => __DIR__.'/../database/database.sqlite',
            ];
        }

        $this->connection = new Medoo($params);
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

    /**
     * Set a custom instance (useful for testing / DI).
     */
    public static function setInstance(?self $instance): void
    {
        self::$instance = $instance;
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

    public static function mapper(): string
    {
        if (! isset($_ENV['DB_MAPPER'])) {
            self::loadEnv();
        }

        return $_ENV['DB_MAPPER'] ?? 'DogStory';
    }

    public static function author(): string
    {
        if (! isset($_ENV['DB_AUTHOR'])) {
            self::loadEnv();
        }

        return $_ENV['DB_AUTHOR'] ?? 'Companimal';
    }

    public static function categories(): array
    {
        if (! isset($_ENV['DB_CATEGORIES'])) {
            self::loadEnv();
        }

        if (isset($_ENV['DB_CATEGORIES'])) {
            return json_decode($_ENV['DB_CATEGORIES'], true);
        }

        return [
            0 => '기타',
            '행동+심리',
            '의료/건강',
            '감동',
            '입양',
            '재미',
            '장소',
            '상품',
        ];
    }
}
