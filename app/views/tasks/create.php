<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle tâche - TaskManager</title>
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
            <h1>Nouvelle tâche</h1>
            <p class="form-subtitle">Ajoute les details de la tache a suivre.</p>

            <form action="/taskmanager/public/index.php?controller=task&action=store" method="post">
                <p class="form-group">
                    <label for="title">Titre</label>
                    <input type="text" id="title" name="title" required maxlength="150">
                </p>
                <p class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4"></textarea>
                </p>
                <p class="form-group">
                    <label for="status">Statut</label>
                    <select id="status" name="status">
                        <option value="todo">À faire</option>
                        <option value="done">Terminée</option>
                    </select>
                </p>
                <p class="actions">
                    <button type="submit" class="btn btn-primary">Créer</button>
                    <a class="btn btn-outline" href="/taskmanager/public/index.php?controller=task&action=index">Annuler</a>
                </p>
            </form>
        </div>
    </main>
</body>
</html>
