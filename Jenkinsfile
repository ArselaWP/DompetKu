pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                checkout scm
                echo 'Source code DompetKu berhasil diambil'
            }
        }

        stage('Preparation') {
            steps {
                echo 'Persiapan CI/CD DompetKu berhasil'
            }
        }
    }

    post {
        success {
            echo 'Pipeline DompetKu SUCCESS'
        }
        failure {
            echo 'Pipeline DompetKu FAILED'
        }
    }
}
