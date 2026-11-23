pipeline {
    agent any
    stages {
        stage('Test') {
            steps {
                echo 'Jenkinsfile detecte avec succes !'
            }
        }

        stage('SonarCloud Analysis') {
            steps {
                withSonarQubeEnv('SonarCloud') {
                    script {
                        def scannerHome = tool 'SonarScanner'
                        bat "\"${scannerHome}\\bin\\sonar-scanner.bat\" -Dsonar.organization=anouarelghannami -Dsonar.projectKey=anouarelghannami_projetphpp -Dsonar.sources=. -Dsonar.host.url=https://sonarcloud.io"
                    }
                }
            }
        }
    }
}