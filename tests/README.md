Tests automatisés du projet, écrits avec PHPUnit.

## Exécuter les tests

Depuis la racine du projet :

```
php phpunit.phar -c phpunit.xml --testdox
```

`phpunit.phar` est le binaire PHPUnit (téléchargé directement depuis phar.phpunit.de,
pas de Composer nécessaire pour ce petit projet sans framework). Il n'est pas versionné
(voir `.gitignore`) ; s'il est absent, le retélécharger avec :

```
curl -L -o phpunit.phar https://phar.phpunit.de/phpunit-10.phar
```

## Fonctionnement

- [TaskTest.php](TaskTest.php) teste le modèle `Task` (`app/models/Task.php`) : `create`,
  `find`, `all`, `update`, `delete`.
- Les tests utilisent la vraie connexion PDO définie dans `config/database.php` (donc la
  base `taskmanager` doit exister **et MySQL doit être démarré**, sinon `DatabaseTest`
  échoue). Chaque test ouvre une transaction dans `setUp()` et la fait échouer
  (`rollBack`) dans `tearDown()`, donc aucune donnée de test ne persiste.
- En local, démarrer MySQL via le panneau de contrôle XAMPP avant de lancer les tests.
  Dans le pipeline Jenkins, le stage `Check MySQL` s'en charge automatiquement.

Objectif Jenkins :
- vérifier la syntaxe PHP ;
- exécuter les tests ;
- détecter les régressions ;
- préparer le déploiement.
