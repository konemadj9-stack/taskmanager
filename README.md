# TaskManager

Petit projet PHP natif suivant une architecture MVC, prévu pour apprendre GitHub + Jenkins + CI/CD avec XAMPP.

## Stack
- PHP natif, sans framework
- HTML/CSS
- MySQL
- PDO
- Apache/XAMPP

## Installation locale
1. Copier le dossier `taskmanager` dans `C:\Program Files\mes app\xamp\htdocs\` (chemin réel de l'installation XAMPP sur cette machine).
2. Créer une base MySQL nommée `taskmanager`.
3. Importer `database/schema.sql` dans phpMyAdmin.
4. Vérifier les identifiants dans `config/database.php`.
5. S'assurer que MySQL et Apache sont démarrés (via le panneau de contrôle XAMPP) avant de lancer les tests ou d'ouvrir le site.
6. Ouvrir `http://localhost/taskmanager/public/`.

## Structure
- `app/` : MVC
- `config/` : configuration
- `database/` : script SQL
- `public/` : point d'entrée web et ressources publiques
- `tests/` : emplacement des tests
- `Jenkinsfile` : futur pipeline Jenkins
