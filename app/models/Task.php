<?php

class Task
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function all(): array
    {
        $stmt = $this->db->query('SELECT * FROM tasks ORDER BY created_at DESC');

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM tasks WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $task = $stmt->fetch();

        return $task === false ? null : $task;
    }

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

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM tasks WHERE id = :id');

        return $stmt->execute(['id' => $id]);
    }
}
