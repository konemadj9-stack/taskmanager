pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                echo 'Le code est déjà récupéré par Jenkins.'
            }
        }

        stage('PHP syntax check') {
            steps {
                bat 'php -v'
                bat 'php -l public/index.php'
            }
        }

        stage('Tests') {
            steps {
                bat 'if not exist phpunit.phar curl -L -o phpunit.phar https://phar.phpunit.de/phpunit-10.phar'
                bat 'php phpunit.phar -c phpunit.xml --testdox'
            }
        }

        stage('Deploy local') {
            steps {
                echo 'Le déploiement vers XAMPP sera configuré ensuite.'
            }
        }
    }
}
