<?php

class Database
{
    private string $host;
    private string $dbName;
    private string $username;
    private string $password;

    public function __construct()
    {
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->dbName = getenv('DB_NAME') ?: 'taskmanager';
        $this->username = getenv('DB_USER') ?: 'root';

        $password = getenv('DB_PASSWORD');
        $this->password = $password === false ? '' : $password;
    }

    // Ouvre et renvoie une connexion PDO vers MySQL, prête à l'emploi
    // (mode exceptions activé, résultats renvoyés sous forme de tableaux associatifs)
    public function getConnection(): PDO
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset=utf8mb4";

        return new PDO($dsn, $this->username, $this->password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}
