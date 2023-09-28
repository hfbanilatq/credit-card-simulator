pipeline {
    agent {
    kubernetes {
    // This is a YAML representation of the Pod, to allow setting any values not supported as fields.
      yamlFile 'kubernetes/agent.yaml' // Declarative agents can be defined from YAML.
    }
  }

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
        stage('Instalar dependencias') {
            steps {
                sh 'composer install'
            }
        }

        stage('Construir imagen y enviar al repositorio') {
            steps {
                script {
                    docker.withRegistry("https://${ECR_REGISTRY}", "ecr:us-east-1:${AWS_CREDENTIALS_ID}") {
                        docker.build("${ECR_REGISTRY}/${ECR_REPO}:latest", ".")
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
