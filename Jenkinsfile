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
                sh 'export RELEASE_DATE=`date -u +%Y%m%d%H%M%S`'
                sh 'scp -r * ologinov@web1.nprj.ru:/home/ologinov/ts/$RELEASE_DATE'
                //sh 'rsync -azvWP --delete --exclude-from=RSYNC_EXCLUDES . ologinov@web1.nprj.ru:/home/ologinov/ts/'
            }
        }
    }
}