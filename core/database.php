<?php

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $dsn = "mysql:host=localhost;dbname=filetransfer;charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, 'root', '', $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    private function __clone() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance->pdo;
    }
}