<?php

class Task
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // R (Read) : récupère toutes les tâches, les plus récentes en premier
    public function all(): array
    {
        $stmt = $this->db->query('SELECT * FROM tasks ORDER BY created_at DESC');

        return $stmt->fetchAll();
    }

    // R (Read) : récupère une seule tâche par son id, ou null si elle n'existe pas
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM tasks WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $task = $stmt->fetch();

        return $task === false ? null : $task;
    }

    // C (Create) : insère une nouvelle tâche et renvoie son id auto-incrémenté
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO tasks (title, description, status) VALUES (:title, :description, :status)'
        );

        $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'] !== '' ? $data['description'] : null,
            'status' => $data['status'] ?? 'todo',
        ]);

        return (int) $this->db->lastInsertId();
    }

    // U (Update) : met à jour une tâche existante identifiée par son id
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE tasks SET title = :title, description = :description, status = :status WHERE id = :id'
        );

        return $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'] !== '' ? $data['description'] : null,
            'status' => $data['status'] ?? 'todo',
            'id' => $id,
        ]);
    }

    // D (Delete) : supprime une tâche par son id
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM tasks WHERE id = :id');

        return $stmt->execute(['id' => $id]);
    }
}
