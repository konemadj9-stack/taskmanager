<?php

class TaskController
{
    private Task $taskModel;

    public function __construct()
    {
        $database = new Database();
        $this->taskModel = new Task($database->getConnection());
    }

    public function index(): void
    {
        $tasks = $this->taskModel->all();

        require __DIR__ . '/../views/tasks/index.php';
    }

    public function create(): void
    {
        require __DIR__ . '/../views/tasks/create.php';
    }

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

    public function delete(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id > 0) {
            $this->taskModel->delete($id);
        }

        $this->redirectToIndex();
    }

    private function redirectToIndex(): void
    {
        header('Location: /taskmanager/public/index.php?controller=task&action=index');
        exit;
    }
}
