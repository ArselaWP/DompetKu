pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                checkout scm
                echo 'Source code DompetKu berhasil diambil'
            }
        }

        stage('Test') {
            steps {
                bat 'docker build --target test -t dompetku:test .'
            }
        }

        stage('Build Image') {
            steps {
                bat 'docker build --target app -t dompetku:week2 .'
            }
        }
    }

    post {
        success {
            echo 'Test dan Docker Build berhasil!'
        }

        failure {
            echo 'Pipeline gagal, periksa Console Output.'
        }
    }
}
