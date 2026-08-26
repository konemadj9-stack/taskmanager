<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/Task.php';

class TaskTest extends TestCase
{
    private PDO $db;
    private Task $task;

    protected function setUp(): void
    {
        $this->db = (new Database())->getConnection();
        $this->db->beginTransaction();
        $this->task = new Task($this->db);
    }

    protected function tearDown(): void
    {
        $this->db->rollBack();
    }

    public function testCreateInsertsATaskAndReturnsItsId(): void
    {
        $id = $this->task->create([
            'title' => 'Ecrire un test',
            'description' => 'Verifier que create() fonctionne',
            'status' => 'todo',
        ]);

        $this->assertGreaterThan(0, $id);

        $found = $this->task->find($id);
        $this->assertSame('Ecrire un test', $found['title']);
        $this->assertSame('todo', $found['status']);
    }

    public function testCreateDefaultsStatusToTodoWhenNotProvided(): void
    {
        $id = $this->task->create(['title' => 'Sans statut precise', 'description' => '']);

        $found = $this->task->find($id);
        $this->assertSame('todo', $found['status']);
    }

    public function testCreateStoresNullDescriptionWhenEmpty(): void
    {
        $id = $this->task->create(['title' => 'Sans description', 'description' => '']);

        $found = $this->task->find($id);
        $this->assertNull($found['description']);
    }

    public function testFindReturnsNullForUnknownId(): void
    {
        $this->assertNull($this->task->find(999999));
    }

    public function testAllIncludesEveryCreatedTask(): void
    {
        $firstId = $this->task->create(['title' => 'Premiere tache', 'description' => '']);
        $secondId = $this->task->create(['title' => 'Deuxieme tache', 'description' => '']);

        $ids = array_column($this->task->all(), 'id');

        $this->assertContains($firstId, $ids);
        $this->assertContains($secondId, $ids);
    }

    public function testAllReturnsEmptyArrayWhenNoTasksExist(): void
    {
        $this->db->exec('DELETE FROM tasks');

        $this->assertSame([], $this->task->all());
    }

    public function testUpdateChangesTitleAndStatus(): void
    {
        $id = $this->task->create(['title' => 'Titre original', 'description' => '', 'status' => 'todo']);

        $result = $this->task->update($id, [
            'title' => 'Titre modifie',
            'description' => 'Nouvelle description',
            'status' => 'done',
        ]);

        $this->assertTrue($result);

        $found = $this->task->find($id);
        $this->assertSame('Titre modifie', $found['title']);
        $this->assertSame('done', $found['status']);
    }

    public function testDeleteRemovesTheTask(): void
    {
        $id = $this->task->create(['title' => 'A supprimer', 'description' => '']);

        $this->task->delete($id);

        $this->assertNull($this->task->find($id));
    }
}
