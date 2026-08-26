pipeline {
    agent any

    stages {
        // Le vrai checkout Git a déjà eu lieu automatiquement avant ce stage
        // (le job est configuré en "Pipeline script from SCM"), donc ici on ne fait qu'un echo.
        stage('Checkout') {
            steps {
                echo 'Le code est déjà récupéré par Jenkins.'
            }
        }

        // Vérifie que le code PHP est syntaxiquement valide avant d'aller plus loin
        stage('PHP syntax check') {
            steps {
                bat 'php -v'
                bat 'php -l public/index.php'
            }
        }

        // Télécharge PHPUnit s'il n'est pas déjà présent dans le workspace, puis lance
        // la suite de tests. Un test en échec fait échouer tout le build (build rouge).
        stage('Tests') {
            steps {
                bat 'if not exist phpunit.phar curl -L -o phpunit.phar https://phar.phpunit.de/phpunit-10.phar'
                bat 'php phpunit.phar -c phpunit.xml --testdox'
            }
        }

        // Placeholder : le vrai déploiement vers XAMPP sera ajouté plus tard
        stage('Deploy local') {
            steps {
                echo 'Le déploiement vers XAMPP sera configuré ensuite.'
            }
        }
    }
}
