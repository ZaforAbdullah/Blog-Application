pipeline {
    agent any
    environment {
        registry = credentials('repo_image') //'repository/image'
        registryCredential = 'DockerHub' // Credentials of DockerHub (UserName & Personal access token)
        dockerImage = ''
        REMOTE_HOST = credentials('REMOTE_HOST') //Remote server IP address/domain
        REMOTE_USER = credentials('REMOTE_USER') //Remote server  user name
    }

    stages {
        stage('Build') {
            steps {
                sh 'docker compose -f docker-compose.dev.yml up -d --force-recreate --build'
            }
        }

        stage('Configuration for test') {
            steps {
                sh 'pwd'
                sh "docker exec -i local /bin/bash -c 'composer update'"
                sh "docker exec -i local /bin/bash -c '/usr/local/bin/php artisan config:clear'"
                sh "docker exec -i local /bin/bash -c '/usr/local/bin/php artisan cache:clear'"
                sh "docker exec -i local /bin/bash -c '/usr/local/bin/php artisan view:clear'"
                sh "docker exec -i local /bin/bash -c '/usr/local/bin/php artisan DBConnection'"
                sh "docker exec -i local /bin/bash -c '/usr/local/bin/php artisan migrate'"
                echo 'Application is ready for test'
            }
        }

        stage('PHPUnit test') {
            steps {
                sh "docker exec -i local /bin/bash -c '/usr/local/bin/php artisan test'"
            }
        }
        
        stage('Larastan static code analysis') {
            steps {
                sh "docker exec -i local /bin/bash -c './vendor/bin/phpstan analyse --generate-baseline --memory-limit=2G'"
            }
        }

        stage('Push image to DockerHub') {
            steps {
                script {
                    docker.withRegistry('', registryCredential) {
                        dockerImage = docker.build registry
                        dockerImage.push()
                    }
                }
            }
        }

        stage('Deploy remote server') {
            steps {
                // SSH is "SSH username with private key"
                // Public key need to add on authorized_keys of remote server
                // known_hosts is required for ssh connection, need to create known_hosts as secret file
                // cmd below of example to get key of host
                // ssh-keyscan -H [REMOTE_HOST] >> known_hosts

                withCredentials([sshUserPrivateKey(credentialsId: 'SSH', keyFileVariable: 'identity', passphraseVariable: '', usernameVariable: 'userName'), file(credentialsId: 'known_hosts', variable: 'known_hosts')]) {
                    script {
                        def remote = [:]
                        remote.user = userName
                        remote.name = REMOTE_HOST
                        remote.host = REMOTE_HOST
                        remote.identityFile = identity
                        remote.knownHosts = known_hosts

                        sshCommand remote: remote, command: 'cd laravel; git pull'
                        sshCommand remote: remote, command: "cd laravel; export IMAGE_NAME=${registry}; docker compose down"
                        sshCommand remote: remote, command: "docker image rm ${registry}"
                        sshCommand remote: remote, command: "cd laravel; export IMAGE_NAME=${registry}; docker compose up --detach --force-recreate --build --no-deps"
                    }
                }
            }
        }
    }

    post {
        always {
            sh "docker compose -f docker-compose.dev.yml down"
            sh 'docker volume prune -f'
            sh 'docker image prune'
            cleanWs()
            deleteDir()
        }
    }
}
