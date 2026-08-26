<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/database.php';

class DatabaseTest extends TestCase
{
    public function testGetConnectionReturnsAWorkingPdoInstance(): void
    {
        $connection = (new Database())->getConnection();

        $this->assertInstanceOf(PDO::class, $connection);
        $this->assertEquals(1, $connection->query('SELECT 1')->fetchColumn());
    }

    public function testGetConnectionThrowsOnErrorsByDefault(): void
    {
        $connection = (new Database())->getConnection();

        $this->assertSame(
            PDO::ERRMODE_EXCEPTION,
            $connection->getAttribute(PDO::ATTR_ERRMODE)
        );
    }
}
