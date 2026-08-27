<?php

class TaskController
{
    private Task $taskModel;

    public function __construct()
    {
        // Le controller ouvre lui-même la connexion et instancie le modèle
        // (pas de framework, pas d'injection de dépendances ici)
        $database = new Database();
        $this->taskModel = new Task($database->getConnection());
    }

    // Affiche la liste de toutes les tâches (vue : app/views/tasks/index.php)
    public function index(): void
    {
        $tasks = $this->taskModel->all();

        require __DIR__ . '/../views/tasks/index.php';
    }

    // Affiche le formulaire de création d'une tâche (vue : app/views/tasks/create.php)
    public function create(): void
    {
        require __DIR__ . '/../views/tasks/create.php';
    }

    // Traite la soumission du formulaire de création (POST) puis redirige vers la liste
    public function store(): void
    {
        $title = trim($_POST['title'] ?? '');

        if ($title !== '') {
            $this->taskModel->create([
                'title' => $title,
                'description' => trim($_POST['description'] ?? ''),
                'status' => $_POST['status'] ?? 'todo',
            ]);
        }

        $this->redirectToIndex();
    }

    // Affiche le formulaire d'édition pré-rempli pour la tâche demandée (?id=...)
    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $task = $this->taskModel->find($id);

        if ($task === null) {
            http_response_code(404);
            exit('Tâche introuvable');
        }

        require __DIR__ . '/../views/tasks/edit.php';
    }

    // Traite la soumission du formulaire d'édition (POST) puis redirige vers la liste
    public function update(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');

        if ($id > 0 && $title !== '') {
            $this->taskModel->update($id, [
                'title' => $title,
                'description' => trim($_POST['description'] ?? ''),
                'status' => $_POST['status'] ?? 'todo',
            ]);
        }

        $this->redirectToIndex();
    }

    // Supprime la tâche demandée (?id=...) puis redirige vers la liste
    public function delete(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id > 0) {
            $this->taskModel->delete($id);
        }

        $this->redirectToIndex();
    }

    // Exporte la liste des tâches au format CSV, compatible Excel (BOM UTF-8
    // pour les accents, ';' comme séparateur car c'est ce qu'Excel FR attend)
    public function exportCsv(): void
    {
        $tasks = $this->taskModel->all();

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="taches_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fwrite($output, "\xEF\xBB\xBF");
        fputcsv($output, ['Titre', 'Description', 'Statut', 'Créée le'], ';');

        foreach ($tasks as $task) {
            fputcsv($output, [
                $task['title'],
                $task['description'] ?? '',
                $task['status'] === 'done' ? 'Terminée' : 'À faire',
                $task['created_at'],
            ], ';');
        }

        fclose($output);
        exit;
    }

    // Affiche une version imprimable de la liste (vue : app/views/tasks/printable.php).
    // L'utilisateur peut choisir "Enregistrer au format PDF" dans la boîte
    // d'impression du navigateur, ouverte automatiquement par cette vue.
    public function printable(): void
    {
        $tasks = $this->taskModel->all();

        require __DIR__ . '/../views/tasks/printable.php';
    }

    // Petit utilitaire commun à store(), update() et delete() : retour à la liste des tâches
    private function redirectToIndex(): void
    {
        header('Location: /taskmanager/public/index.php?controller=task&action=index');
        exit;
    }
}
