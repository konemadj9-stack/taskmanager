<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tâches - TaskManager</title>
    <link rel="stylesheet" href="/taskmanager/public/assets/css/style.css">
</head>
<body>
    <header class="topbar">
        <div class="topbar-inner">
            <a class="brand" href="/taskmanager/public/index.php">TaskManager</a>
            <nav class="topbar-actions">
                <a href="/taskmanager/public/index.php">Accueil</a>
                <a class="btn btn-primary" href="/taskmanager/public/index.php?controller=task&action=create">+ Nouvelle tâche</a>
            </nav>
        </div>
    </header>

    <main class="page">
        <div class="page-head">
            <h1>Mes tâches</h1>
            <span class="task-count"><?= count($tasks) ?> tâche<?= count($tasks) > 1 ? 's' : '' ?></span>
        </div>

        <?php if (empty($tasks)): ?>
            <div class="empty-state">
                <p class="empty-state-icon">📋</p>
                <p>Aucune tâche pour le moment.</p>
                <a class="btn btn-primary" href="/taskmanager/public/index.php?controller=task&action=create">Créer ma première tâche</a>
            </div>
        <?php else: ?>
            <div class="card table-wrapper">
                <table class="tasks">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Description</th>
                            <th>Statut</th>
                            <th>Créée le</th>
                            <th class="col-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <td class="col-title"><?= htmlspecialchars($task['title']) ?></td>
                                <td class="col-description"><?= nl2br(htmlspecialchars($task['description'] ?? '')) ?></td>
                                <td>
                                    <span class="badge <?= $task['status'] === 'done' ? 'badge-done' : 'badge-todo' ?>">
                                        <?= $task['status'] === 'done' ? 'Terminée' : 'À faire' ?>
                                    </span>
                                </td>
                                <td class="col-date"><?= htmlspecialchars($task['created_at']) ?></td>
                                <td class="actions">
                                    <a class="btn btn-sm btn-outline" href="/taskmanager/public/index.php?controller=task&action=edit&id=<?= (int) $task['id'] ?>">Modifier</a>
                                    <a class="btn btn-sm btn-danger" href="/taskmanager/public/index.php?controller=task&action=delete&id=<?= (int) $task['id'] ?>"
                                       onclick="return confirm('Supprimer cette tâche ?');">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
