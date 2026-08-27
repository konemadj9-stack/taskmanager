<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes tâches - Impression</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 30px; color: #1e293b; }
        h1 { font-size: 1.5rem; margin-bottom: 4px; }
        p.date { color: #64748b; margin-top: 0; margin-bottom: 24px; font-size: 0.9rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; font-size: 0.9rem; }
        th { background: #f1f5f9; }
        .no-print { margin-bottom: 20px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <p class="no-print"><a href="/taskmanager/public/index.php?controller=task&action=index">&larr; Retour à la liste</a></p>
    <h1>Mes tâches — TaskManager</h1>
    <p class="date">Exporté le <?= htmlspecialchars(date('d/m/Y à H:i')) ?></p>

    <?php if (empty($tasks)): ?>
        <p>Aucune tâche.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Statut</th>
                    <th>Créée le</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td><?= htmlspecialchars($task['title']) ?></td>
                        <td><?= nl2br(htmlspecialchars($task['description'] ?? '')) ?></td>
                        <td><?= $task['status'] === 'done' ? 'Terminée' : 'À faire' ?></td>
                        <td><?= htmlspecialchars($task['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <script>
        // Ouvre automatiquement la boite d'impression : l'utilisateur choisit
        // "Enregistrer au format PDF" comme destination pour telecharger un PDF.
        window.onload = function () {
            window.print();
        };
    </script>
</body>
</html>
