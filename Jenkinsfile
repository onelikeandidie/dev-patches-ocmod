pipeline {
    agent any
    tools {
        git 'Default'
    }
    environment {
        HOME = '.'
    }
    stages {
        stage ('Build Artifacts') {
            steps {
                script {
                    // Install dependencies
                    sh 'sh ./build.sh'
                    archiveArtifacts artifacts: 'dist/*', fingerprint: true
                }
            }
        }
    }
}