<?php

class Database
{
    private string $host = 'localhost';
    private string $dbName = 'taskmanager';
    private string $username = 'root';
    private string $password = '';

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
