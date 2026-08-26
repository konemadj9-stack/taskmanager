<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la tâche - TaskManager</title>
    <link rel="stylesheet" href="/taskmanager/public/assets/css/style.css">
</head>
<body>
    <main class="container">
        <h1>Modifier la tâche</h1>

        <form action="/taskmanager/public/index.php?controller=task&action=update" method="post">
            <input type="hidden" name="id" value="<?= (int) $task['id'] ?>">
            <p>
                <label for="title">Titre</label><br>
                <input type="text" id="title" name="title" required maxlength="150" value="<?= htmlspecialchars($task['title']) ?>">
            </p>
            <p>
                <label for="description">Description</label><br>
                <textarea id="description" name="description" rows="4"><?= htmlspecialchars($task['description'] ?? '') ?></textarea>
            </p>
            <p>
                <label for="status">Statut</label><br>
                <select id="status" name="status">
                    <option value="todo" <?= $task['status'] === 'todo' ? 'selected' : '' ?>>À faire</option>
                    <option value="done" <?= $task['status'] === 'done' ? 'selected' : '' ?>>Terminée</option>
                </select>
            </p>
            <p class="actions">
                <button type="submit">Enregistrer</button>
                <a href="/taskmanager/public/index.php?controller=task&action=index">Annuler</a>
            </p>
        </form>
    </main>
</body>
</html>
