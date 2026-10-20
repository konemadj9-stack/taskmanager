<?php
$completedTasks = count(array_filter($tasks, static fn (array $task): bool => $task['status'] === 'done'));
$pendingTasks = count($tasks) - $completedTasks;
$completionRate = $tasks === [] ? 0 : (int) round(($completedTasks / count($tasks)) * 100);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tâches - TaskManager</title>
    <link rel="stylesheet" href="/taskmanager/public/assets/css/style.css?v=5">
</head>
<body class="bg-photo bg-photo--tasks">
    <header class="topbar">
        <div class="topbar-inner">
            <a class="brand" href="/taskmanager/public/index.php">TaskManager</a>
            <nav class="topbar-actions">
                <a href="/taskmanager/public/index.php">Accueil</a>
            </nav>
        </div>
    </header>

    <main class="page task-page">
        <section class="task-hero">
            <div>
                <p class="eyebrow">ESPACE DE TRAVAIL</p>
                <h1>Mes tâches</h1>
                <p class="task-hero-copy">Organisez votre journée, une tâche à la fois.</p>
            </div>
            <a class="btn btn-primary task-create-button" href="/taskmanager/public/index.php?controller=task&action=create">
                <span aria-hidden="true">＋</span> Nouvelle tâche
            </a>
        </section>

        <section class="task-overview" aria-label="Résumé des tâches">
            <article class="overview-card overview-card--total">
                <span class="overview-icon" aria-hidden="true">▤</span>
                <div>
                    <p class="overview-label">Toutes les tâches</p>
                    <p class="overview-value"><?= count($tasks) ?></p>
                </div>
            </article>
            <article class="overview-card overview-card--pending">
                <span class="overview-icon" aria-hidden="true">◷</span>
                <div>
                    <p class="overview-label">À faire</p>
                    <p class="overview-value"><?= $pendingTasks ?></p>
                </div>
            </article>
            <article class="overview-card overview-card--done">
                <span class="overview-icon" aria-hidden="true">✓</span>
                <div>
                    <p class="overview-label">Terminées</p>
                    <p class="overview-value"><?= $completedTasks ?></p>
                </div>
            </article>
            <article class="overview-card overview-card--progress">
                <div class="progress-heading">
                    <p class="overview-label">Progression</p>
                    <p class="overview-value"><?= $completionRate ?><span>%</span></p>
                </div>
                <div class="progress-track" role="progressbar" aria-label="Progression des tâches"
                     aria-valuenow="<?= $completionRate ?>" aria-valuemin="0" aria-valuemax="100">
                    <span style="width: <?= $completionRate ?>%"></span>
                </div>
            </article>
        </section>

        <?php if (empty($tasks)): ?>
            <section class="task-empty-state">
                <span class="empty-illustration" aria-hidden="true">✓</span>
                <p class="eyebrow">TOUT EST PRÊT</p>
                <h2>Votre espace est encore vide</h2>
                <p>Créez une première tâche pour commencer à suivre vos projets.</p>
                <a class="btn btn-primary" href="/taskmanager/public/index.php?controller=task&action=create">Créer ma première tâche</a>
            </section>
        <?php else: ?>
            <section class="task-list-panel">
                <div class="task-list-heading">
                    <div>
                        <h2>Votre liste</h2>
                        <p><span id="visible-task-count"><?= count($tasks) ?></span> <span id="visible-task-label"><?= count($tasks) > 1 ? 'tâches affichées' : 'tâche affichée' ?></span></p>
                    </div>
                    <div class="task-tools">
                        <label class="task-search">
                            <span aria-hidden="true">⌕</span>
                            <span class="visually-hidden">Rechercher une tâche</span>
                            <input id="task-search-input" type="search" placeholder="Rechercher..." autocomplete="off">
                        </label>
                        <div class="task-filters" role="group" aria-label="Filtrer les tâches">
                            <button type="button" class="filter-button is-active" data-filter="all" aria-pressed="true">Toutes</button>
                            <button type="button" class="filter-button" data-filter="todo" aria-pressed="false">À faire</button>
                            <button type="button" class="filter-button" data-filter="done" aria-pressed="false">Terminées</button>
                        </div>
                    </div>
                </div>
                <div class="task-list-actions">
                    <span class="task-list-hint">Gardez le cap sur vos priorités.</span>
                    <div>
                        <a class="text-action" href="/taskmanager/public/index.php?controller=task&action=exportCsv">Exporter CSV</a>
                        <a class="text-action" href="/taskmanager/public/index.php?controller=task&action=printable" target="_blank" rel="noopener">Version imprimable</a>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="tasks task-table">
                        <thead>
                            <tr>
                                <th>Tâche</th>
                                <th>Description</th>
                                <th>Statut</th>
                                <th>Créée le</th>
                                <th class="col-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="task-table-body">
                            <?php foreach ($tasks as $task): ?>
                                <tr data-status="<?= htmlspecialchars($task['status'], ENT_QUOTES, 'UTF-8') ?>">
                                    <td class="col-title">
                                        <span class="task-title-mark <?= $task['status'] === 'done' ? 'is-done' : '' ?>" aria-hidden="true"><?= $task['status'] === 'done' ? '✓' : '' ?></span>
                                        <span><?= htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </td>
                                    <td class="col-description"><?= nl2br(htmlspecialchars($task['description'] ?? '', ENT_QUOTES, 'UTF-8')) ?></td>
                                    <td>
                                        <span class="badge <?= $task['status'] === 'done' ? 'badge-done' : 'badge-todo' ?>">
                                            <span class="status-dot" aria-hidden="true"></span>
                                            <?= $task['status'] === 'done' ? 'Terminée' : 'À faire' ?>
                                        </span>
                                    </td>
                                    <td class="col-date"><?= htmlspecialchars($task['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="actions task-row-actions">
                                        <a class="btn btn-sm btn-outline" href="/taskmanager/public/index.php?controller=task&action=edit&id=<?= (int) $task['id'] ?>">Modifier</a>
                                        <a class="btn btn-sm btn-danger" href="/taskmanager/public/index.php?controller=task&action=delete&id=<?= (int) $task['id'] ?>"
                                           onclick="return confirm('Supprimer cette tâche ?');">Supprimer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p class="task-no-results" id="task-no-results" hidden>Aucune tâche ne correspond à votre recherche.</p>
                </div>
                <div class="task-list-footer">Affichage local · <?= count($tasks) ?> tâche<?= count($tasks) > 1 ? 's' : '' ?> au total</div>
            </section>
        <?php endif; ?>
    </main>
    <?php if (!empty($tasks)): ?>
        <script>
            const taskSearch = document.getElementById('task-search-input');
            const taskRows = Array.from(document.querySelectorAll('#task-table-body tr'));
            const filterButtons = Array.from(document.querySelectorAll('.filter-button'));
            const visibleCount = document.getElementById('visible-task-count');
            const visibleLabel = document.getElementById('visible-task-label');
            const noResults = document.getElementById('task-no-results');
            let activeFilter = 'all';

            function filterTasks() {
                const searchText = taskSearch.value.trim().toLocaleLowerCase('fr');
                let visibleTasks = 0;

                taskRows.forEach((row) => {
                    const matchesStatus = activeFilter === 'all' || row.dataset.status === activeFilter;
                    const matchesSearch = row.textContent.toLocaleLowerCase('fr').includes(searchText);
                    const isVisible = matchesStatus && matchesSearch;
                    row.hidden = !isVisible;
                    visibleTasks += isVisible ? 1 : 0;
                });

                visibleCount.textContent = visibleTasks;
                visibleLabel.textContent = visibleTasks > 1 ? 'tâches affichées' : 'tâche affichée';
                noResults.hidden = visibleTasks !== 0;
            }

            taskSearch.addEventListener('input', filterTasks);
            filterButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    activeFilter = button.dataset.filter;
                    filterButtons.forEach((filterButton) => {
                        const isActive = filterButton === button;
                        filterButton.classList.toggle('is-active', isActive);
                        filterButton.setAttribute('aria-pressed', String(isActive));
                    });
                    filterTasks();
                });
            });
        </script>
    <?php endif; ?>
</body>
</html>
