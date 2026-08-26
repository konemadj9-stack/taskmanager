<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle tâche - TaskManager</title>
    <link rel="stylesheet" href="/taskmanager/public/assets/css/style.css?v=2">
</head>
<body>
    <main class="container">
        <h1>Nouvelle tâche</h1>

        <form action="/taskmanager/public/index.php?controller=task&action=store" method="post">
            <p>
                <label for="title">Titre</label><br>
                <input type="text" id="title" name="title" required maxlength="150">
            </p>
            <p>
                <label for="description">Description</label><br>
                <textarea id="description" name="description" rows="4"></textarea>
            </p>
            <p>
                <label for="status">Statut</label><br>
                <select id="status" name="status">
                    <option value="todo">À faire</option>
                    <option value="done">Terminée</option>
                </select>
            </p>
            <p class="actions">
                <button type="submit">Créer</button>
                <a href="/taskmanager/public/index.php?controller=task&action=index">Annuler</a>
            </p>
        </form>
    </main>
</body>
</html>
