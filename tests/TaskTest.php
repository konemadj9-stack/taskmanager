<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/Task.php';

class TaskTest extends TestCase
{
    private PDO $db;
    private Task $task;

    // Avant chaque test : connexion réelle à MySQL, puis ouverture d'une transaction.
    // Tout ce que le test fait (create, update, delete...) restera invisible en dehors du test.
    protected function setUp(): void
    {
        $this->db = (new Database())->getConnection();
        $this->db->beginTransaction();
        $this->task = new Task($this->db);
    }

    // TEST TEMPORAIRE : echoue volontairement pour observer le comportement du
    // pipeline Jenkins sur un build rouge (le stage Tests doit echouer et bloquer
    // le deploiement). A supprimer une fois la demonstration faite.
    public function testDemonstrationEchecVolontaireDuPipeline(): void
    {
        $this->assertSame(1, 2, 'Echec volontaire pour tester le pipeline Jenkins.');
    }

    // Après chaque test : on annule la transaction, donc aucune donnée de test
    // ne reste dans la table (les vraies tâches ne sont jamais touchées).
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

    // Vide volontairement la table (dans la transaction du test) pour vérifier
    // qu'all() renvoie bien un tableau vide, et pas null ou une erreur.
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

    // update() ne vérifie pas l'existence de l'id : la requête s'exécute sans erreur
    // même si aucune ligne ne correspond. Ce test documente ce comportement.
    public function testUpdateOnUnknownIdChangesNothing(): void
    {
        $this->task->update(999999, [
            'title' => 'Fantome',
            'description' => '',
            'status' => 'done',
        ]);

        $this->assertNull($this->task->find(999999));
    }

    public function testUpdateStoresNullDescriptionWhenEmpty(): void
    {
        $id = $this->task->create([
            'title' => 'Avec description',
            'description' => 'Une description',
            'status' => 'todo',
        ]);

        $this->task->update($id, [
            'title' => 'Avec description',
            'description' => '',
            'status' => 'todo',
        ]);

        $found = $this->task->find($id);
        $this->assertNull($found['description']);
    }

    public function testDeleteRemovesTheTask(): void
    {
        $id = $this->task->create(['title' => 'A supprimer', 'description' => '']);

        $this->task->delete($id);

        $this->assertNull($this->task->find($id));
    }

    // delete() ne vérifie pas l'existence de l'id : la requête s'exécute sans erreur
    // même si aucune ligne ne correspond, et ne touche à aucune autre tâche.
    public function testDeleteOnUnknownIdDoesNothing(): void
    {
        $id = $this->task->create(['title' => 'A conserver', 'description' => '']);

        $result = $this->task->delete(999999);

        $this->assertTrue($result);
        $this->assertNotNull($this->task->find($id));
    }
}
