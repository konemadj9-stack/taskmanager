<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la tâche - TaskManager</title>
    <link rel="stylesheet" href="/taskmanager/public/assets/css/style.css?v=3">
</head>
<body class="bg-photo bg-photo--form">
    <header class="topbar">
        <div class="topbar-inner">
            <a class="brand" href="/taskmanager/public/index.php">TaskManager</a>
            <nav class="topbar-actions">
                <a href="/taskmanager/public/index.php">Accueil</a>
                <a href="/taskmanager/public/index.php?controller=task&action=index">Mes tâches</a>
            </nav>
        </div>
    </header>

    <main class="form-page">
        <div class="container form-card">
            <h1>Modifier la tâche</h1>
            <p class="form-subtitle">Mets a jour les details de la tache.</p>

            <form action="/taskmanager/public/index.php?controller=task&action=update" method="post">
                <input type="hidden" name="id" value="<?= (int) $task['id'] ?>">
                <p class="form-group">
                    <label for="title">Titre</label>
                    <input type="text" id="title" name="title" required maxlength="150" value="<?= htmlspecialchars($task['title']) ?>">
                </p>
                <p class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4"><?= htmlspecialchars($task['description'] ?? '') ?></textarea>
                </p>
                <p class="form-group">
                    <label for="status">Statut</label>
                    <select id="status" name="status">
                        <option value="todo" <?= $task['status'] === 'todo' ? 'selected' : '' ?>>À faire</option>
                        <option value="done" <?= $task['status'] === 'done' ? 'selected' : '' ?>>Terminée</option>
                    </select>
                </p>
                <p class="actions">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a class="btn btn-outline" href="/taskmanager/public/index.php?controller=task&action=index">Annuler</a>
                </p>
            </form>
        </div>
    </main>
</body>
</html>
