Tests automatisés du projet, écrits avec PHPUnit.

## Exécuter les tests

Depuis la racine du projet :

```
php phpunit.phar -c phpunit.xml --testdox
```

`phpunit.phar` est le binaire PHPUnit (téléchargé directement depuis phar.phpunit.de,
pas de Composer nécessaire pour ce petit projet sans framework). Il n'est pas versionné
(voir `.gitignore`) ; s'il est absent, le télécharger avec :

```
curl -L -o phpunit.phar https://phar.phpunit.de/phpunit-10.phar
```

## Fonctionnement

- [TaskTest.php](TaskTest.php) teste le modèle `Task` (`app/models/Task.php`) : `create`,
  `find`, `all`, `update`, `delete`.
- Les tests utilisent la vraie connexion PDO définie dans `config/database.php`. La base
  `taskmanager` doit exister et MySQL doit être démarré. Les identifiants peuvent être
  fournis avec `DB_HOST`, `DB_NAME`, `DB_USER` et `DB_PASSWORD`.
- Chaque test de modèle ouvre une transaction et l'annule dans `tearDown()`. Utilisez une
  base réservée aux tests : le test `testAllReturnsEmptyArrayWhenNoTasksExist` supprime
  les lignes de `tasks` dans sa transaction.
- Avec XAMPP, les valeurs par défaut restent `localhost`, `taskmanager`, `root` et mot de
  passe vide. Le pipeline Jenkins attend le compte MySQL stocké dans le credential
  `taskmanager-ci-db` et une base initialisée séparément.

Le pipeline vérifie aussi PHPStan, la syntaxe Terraform et celle d'Ansible. Il ne crée
pas la VM et ne déploie pas l'application.
