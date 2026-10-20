pipeline {
    agent any

    triggers {
        pollSCM('H/2 * * * *')
    }

    environment {
        TF_IN_AUTOMATION = 'true'
        DB_HOST = '127.0.0.1'
        DB_NAME = 'taskmanager'
        TASKMANAGER_HOST = '192.168.56.102'
    }

    stages {
        stage('PHP syntax') {
            steps {
                sh '''
                    find app config public tests -type f -name '*.php' -print0 |
                        xargs -0 -r -n1 php -l
                '''
            }
        }

        stage('PHPStan') {
            steps {
                sh '''
                    if [ ! -f phpstan.phar ]; then
                        curl -fsSL https://github.com/phpstan/phpstan/releases/latest/download/phpstan.phar -o phpstan.phar
                    fi
                    php phpstan.phar analyse -c phpstan.neon --no-progress
                '''
            }
        }

        stage('PHPUnit') {
            steps {
                withCredentials([usernamePassword(
                    credentialsId: 'taskmanager-ci-db',
                    usernameVariable: 'DB_USER',
                    passwordVariable: 'DB_PASSWORD'
                )]) {
                    sh '''
                        if [ ! -f phpunit.phar ]; then
                            curl -fsSL https://phar.phpunit.de/phpunit-10.phar -o phpunit.phar
                        fi
                        php phpunit.phar -c phpunit.xml --testdox
                    '''
                }
            }
        }

        stage('Terraform validation') {
            steps {
                dir('terraform') {
                    sh '''
                        terraform fmt -check -recursive
                        terraform init -backend=false -input=false
                        terraform validate
                    '''
                }
            }
        }

        stage('Ansible syntax') {
            steps {
                sh '''
                    ansible-galaxy collection install -r ansible/requirements.yml
                    ansible-playbook --syntax-check -i ansible/inventory.ini ansible/site.yml
                '''
            }
        }

        stage('Deploy to Ubuntu') {
            steps {
                withCredentials([
                    sshUserPrivateKey(
                        credentialsId: 'taskmanager-ubuntu-ssh',
                        keyFileVariable: 'SSH_KEY'
                    ),
                    string(
                        credentialsId: 'taskmanager-ubuntu-db-password',
                        variable: 'TASKMANAGER_DB_PASSWORD'
                    )
                ]) {
                    sh '''
                        set +x
                        extra_vars="$(mktemp)"
                        trap 'rm -f "$extra_vars"' EXIT
                        chmod 600 "$extra_vars"
                        printf '{"ansible_host":"%s","taskmanager_db_password":"%s"}\\n' \
                            "$TASKMANAGER_HOST" "$TASKMANAGER_DB_PASSWORD" > "$extra_vars"
                        ansible-playbook \
                            -i ansible/inventory.ini \
                            ansible/site.yml \
                            --private-key "$SSH_KEY" \
                            --extra-vars "@$extra_vars"
                    '''
                }
            }
        }
    }
}
