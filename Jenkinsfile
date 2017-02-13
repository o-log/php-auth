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
                sh 'scp * ologinov@web1.nprj.ru:/home/ologinov/ts'
            }
        }
    }
}