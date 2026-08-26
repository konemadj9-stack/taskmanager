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

        // DatabaseTest.php ouvre une vraie connexion PDO vers MySQL : sans ce stage,
        // un MySQL arrêté ferait échouer le build pour une mauvaise raison.
        // On démarre mysqld en arrière-plan (start /B) si besoin, puis on attend
        // qu'il réponde au ping avant de continuer.
        stage('Check MySQL') {
            steps {
                bat '''
                    set "MYSQL_BIN=C:\\Program Files\\mes app\\xamp\\mysql\\bin"

                    tasklist /FI "IMAGENAME eq mysqld.exe" | find /I "mysqld.exe" >nul
                    if errorlevel 1 (
                        echo MySQL n'est pas demarre, demarrage en arriere-plan...
                        start "" /B "%MYSQL_BIN%\\mysqld.exe" --defaults-file="%MYSQL_BIN%\\my.ini" --standalone
                    ) else (
                        echo MySQL est deja demarre.
                    )

                    set ATTEMPTS=0
                    :waitloop
                    "%MYSQL_BIN%\\mysqladmin.exe" --user=root ping >nul 2>&1
                    if not errorlevel 1 goto ready
                    set /A ATTEMPTS+=1
                    if %ATTEMPTS% GEQ 15 (
                        echo MySQL n'a pas repondu a temps.
                        exit /b 1
                    )
                    timeout /T 2 >nul
                    goto waitloop

                    :ready
                    echo MySQL est pret.
                '''
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

        // Copie le projet vers le htdocs de XAMPP (chemin réel de l'installation sur
        // cette machine, différent du C:\xampp par défaut). /MIR reflète exactement le
        // workspace ; on exclut le repo Git et les fichiers/artefacts liés aux tests.
        stage('Deploy local') {
            steps {
                bat '''
                    robocopy . "C:\\Program Files\\mes app\\xamp\\htdocs\\taskmanager" /MIR /XD .git .phpunit.cache /XF phpunit.phar
                    if %ERRORLEVEL% LEQ 7 (
                        exit /b 0
                    ) else (
                        exit /b 1
                    )
                '''
            }
        }
    }
}
