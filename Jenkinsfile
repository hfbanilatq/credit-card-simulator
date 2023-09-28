pipeline {
    agent any

    environment {
        ECR_REGISTRY = '309682544380.dkr.ecr.us-east-1.amazonaws.com'
        ECR_REPO = 'credit-card-simulator'
        K8S_MANIFESTS_DIR = 'kubernetes/Manifiesto.yml'
        APP_VERSION = "${env.BUILD_NUMBER}"
        AWS_CREDENTIALS_ID = 'AwsCredentials'
    }

    stages {
        stage('Pull del Repositorio') {
            steps {
                checkout scm
            }
        }

        stage('Construir imagen y enviar al repositorio') {
            steps {
                script {
                    docker.withRegistry("${ECR_REGISTRY}", "${AWS_CREDENTIALS_ID}") {
                        docker.build("${ECR_REGISTRY}/${ECR_REPO}:lastes", ".")
                        docker.image("${ECR_REGISTRY}/${ECR_REPO}:latest").push()
                    }  
                }              
            }
        }

        stage('Desplegar en Kubernetes') {
            steps {
                sh "kubectl --kubeconfig=${Kubernetes} apply -f ${K8S_MANIFESTS_DIR}/"
            }
        }
    }
}
