<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/database.php';

class DatabaseTest extends TestCase
{
    // Vérifie que getConnection() renvoie une vraie connexion PDO fonctionnelle
    // (pas juste un objet bien typé : on exécute une requête réelle pour le prouver)
    public function testGetConnectionReturnsAWorkingPdoInstance(): void
    {
        $connection = (new Database())->getConnection();

        $this->assertInstanceOf(PDO::class, $connection);
        $this->assertEquals(1, $connection->query('SELECT 1')->fetchColumn());
    }

    // Vérifie que le mode d'erreur PDO::ERRMODE_EXCEPTION est bien actif,
    // pour être sûr qu'une erreur SQL lève une exception plutôt que d'échouer en silence
    public function testGetConnectionThrowsOnErrorsByDefault(): void
    {
        $connection = (new Database())->getConnection();

        $this->assertSame(
            PDO::ERRMODE_EXCEPTION,
            $connection->getAttribute(PDO::ATTR_ERRMODE)
        );
    }
}
