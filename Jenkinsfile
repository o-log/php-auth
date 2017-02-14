#!groovy
pipeline {
    agent any
    stages {
        stage('build') {
            steps {
                sh 'composer update'
            }
        }
        stage('test') {
            steps {
                sh 'vendor/phpunit/phpunit/phpunit Tests'
            }
        }
        stage('deploy') {
            steps {
                sh 'DEPLOY.sh'
            }
        }
    }
}