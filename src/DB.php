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

        $connection = $_ENV['DB_CONNECTION'] ?? 'sqlite';

        if ($connection == 'mysql') {
            $params = [
                'type' => $connection,
                'host' => $_ENV['DB_HOST'],
                'database' => $_ENV['DB_DATABASE'],
                'username' => $_ENV['DB_USERNAME'],
                'password' => $_ENV['DB_PASSWORD'],
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_general_ci',
                'port' => $_ENV['DB_PORT'],
            ];
        }

        if ($connection == 'sqlite') {
            $databasePath = $_ENV['DB_DATABASE'] ?? __DIR__.'/../database/database.sqlite';

            $params = [
                'type' => 'sqlite',
                'database' => $databasePath,
            ];
        }

        $this->connection = new Medoo($params);
    }

    private static function loadEnv(): void
    {
        $dotenv = Dotenv::createImmutable(getcwd());
        $dotenv->safeLoad();
    }

    public static function getInstance(): static
    {
        if (self::$instance === null) {
            self::$instance = new self;
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
        return self::config()['table'] ?? 'posts';
    }

    public static function mapper(): string
    {
        return getcwd().DIRECTORY_SEPARATOR.'db-to-markdown.json';
    }

    public static function author(): string
    {
        return self::config()['author'] ?? 'Companimal';
    }

    public static function categories(): array
    {
        if (isset(self::config()['category_labels'])) {
            return self::config()['category_labels'];
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

    /**
     * Load the project-wide db-to-markdown.json configuration (cached).
     *
     * @return array<string, mixed>
     */
    private static function config(): array
    {
        static $config = null;

        if ($config === null) {
            $path = self::mapper();

            if (is_file($path)) {
                $decoded = json_decode((string) file_get_contents($path), true);

                $config = is_array($decoded) ? $decoded : [];
            } else {
                $config = [];
            }
        }

        return $config;
    }
}
