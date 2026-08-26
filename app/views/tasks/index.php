<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tâches - TaskManager</title>
    <link rel="stylesheet" href="/taskmanager/public/assets/css/style.css">
</head>
<body>
    <main class="container">
        <h1>Mes tâches</h1>

        <p class="actions">
            <a href="/taskmanager/public/index.php?controller=task&action=create">+ Nouvelle tâche</a>
            &nbsp;|&nbsp;
            <a href="/taskmanager/public/index.php">Accueil</a>
        </p>

        <?php if (empty($tasks)): ?>
            <p>Aucune tâche pour le moment.</p>
        <?php else: ?>
            <table class="tasks">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Statut</th>
                        <th>Créée le</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td><?= htmlspecialchars($task['title']) ?></td>
                            <td><?= nl2br(htmlspecialchars($task['description'] ?? '')) ?></td>
                            <td><?= $task['status'] === 'done' ? 'Terminée' : 'À faire' ?></td>
                            <td><?= htmlspecialchars($task['created_at']) ?></td>
                            <td class="actions">
                                <a href="/taskmanager/public/index.php?controller=task&action=edit&id=<?= (int) $task['id'] ?>">Modifier</a>
                                &nbsp;
                                <a href="/taskmanager/public/index.php?controller=task&action=delete&id=<?= (int) $task['id'] ?>"
                                   onclick="return confirm('Supprimer cette tâche ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</body>
</html>
