pipeline {
    agent any

    environment {
        // Variables de entorno
        ECR_REGISTRY = '309682544380.dkr.ecr.us-east-1.amazonaws.com'
        ECR_REPO = 'credit-card-simulator'
        K8S_MANIFESTS_DIR = './Manifiesto.yml'
        DOCKERFILE_PATH = './Dockerfile'
        APP_VERSION = "${env.BUILD_NUMBER}" // Cambia el tag de versión en cada construcción
    }

    stages {
        stage('Construir y Publicar Imagen Docker') {
            steps {
                script {
                    // Construir la imagen Docker
                    def customImageTag = "${ECR_REGISTRY}/${ECR_REPO}:${APP_VERSION}"
                    def latestImageTag = "${ECR_REGISTRY}/${ECR_REPO}:latest"
                    
                    sh "docker build -t ${customImageTag} -f ${DOCKERFILE_PATH} ."
                    
                    // Autenticarse con ECR
                    withCredentials([[$class: 'AmazonWebServicesCredentialsBinding', accessKeyVariable: 'AWS_ACCESS_KEY_ID', credentialsId: 'AwsCredentials', secretKeyVariable: 'AWS_SECRET_ACCESS_KEY']]) {
                        // Cambiar el tag de la imagen en ECR de 'latest' a la versión actual
                        sh "aws ecr get-login-password --region us-east-1 | docker login --username AWS --password-stdin ${ECR_REGISTRY}"
                        sh "aws ecr batch-check-layer-availability --repository-name ${ECR_REPO} --image-id imageTag=${customImageTag}"
                        
                        // Hacer push de la imagen personalizada
                        sh "docker push ${customImageTag}"
                        
                        // Actualizar el tag 'latest' en ECR con la versión actual
                        sh "aws ecr batch-check-layer-availability --repository-name ${ECR_REPO} --image-id imageTag=${latestImageTag}"
                        sh "aws ecr put-image --repository-name ${ECR_REPO} --image-tag latest --image-manifest '${customImageTag}'"
                    }
                }
            }
        }
        
        stage('Desplegar en Kubernetes') {
            steps {
                // Utilizar kubectl para aplicar los recursos de Kubernetes (Deployment, Servicio, etc.)
                sh "kubectl apply -f ${K8S_MANIFESTS_DIR}"
            }
        }
    }
}
