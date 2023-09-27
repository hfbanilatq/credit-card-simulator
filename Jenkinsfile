pipeline {
    agent any

    environment {
        // Variables de entorno
        ECR_REGISTRY = '309682544380.dkr.ecr.us-east-1.amazonaws.com'
        ECR_REPO = 'credit-card-simulator'
        K8S_MANIFESTS_DIR = 'kubernetes/Manifiesto.yml'
        APP_VERSION = "${env.BUILD_NUMBER}" // Cambia el tag de versión en cada construcción
    }

    stages {
        stage('Pull del Repositorio') {
            steps {
                checkout scm
            }
        }

        stage('Construir y Publicar Imagen Containerd') {
            steps {
                script {
                    // Construir la imagen Containerd
                    def customImageTag = "${ECR_REGISTRY}/${ECR_REPO}:${APP_VERSION}"
                    def latestImageTag = "${ECR_REGISTRY}/${ECR_REPO}:latest"

                    // Asegúrate de que tu Dockerfile esté configurado correctamente para `containerd`
                    
                    // Construir la imagen con `ctr`
                    sh "ctr -n=k8s.io images import . ${customImageTag}"

                    // Autenticarse con ECR
                    withCredentials([[$class: 'AmazonWebServicesCredentialsBinding', accessKeyVariable: 'AWS_ACCESS_KEY_ID', credentialsId: 'AwsCredentials', secretKeyVariable: 'AWS_SECRET_ACCESS_KEY']]) {
                        // Cambiar el tag de la imagen en ECR de 'latest' a la versión actual
                        sh "aws ecr get-login-password --region us-east-1 | docker login --username AWS --password-stdin ${ECR_REGISTRY}"
                        sh "aws ecr batch-check-layer-availability --repository-name ${ECR_REPO} --image-id imageTag=${customImageTag}"

                        // Hacer push de la imagen personalizada
                        sh "ctr -n=k8s.io images tag ${customImageTag} ${latestImageTag}"
                        sh "ctr -n=k8s.io images push ${latestImageTag}"
                        
                        // Actualizar el tag 'latest' en ECR con la versión actual
                        sh "aws ecr batch-check-layer-availability --repository-name ${ECR_REPO} --image-id imageTag=${latestImageTag}"
                        sh "aws ecr put-image --repository-name ${ECR_REPO} --image-tag latest --image-manifest '${customImageTag}'"
                    }
                }
            }
        }

        stage('Desplegar en Kubernetes') {
            steps {
                def kubeconfigCred = credentials('Kubernetes')
                sh "kubectl --kubeconfig=${kubeconfigCred} apply -f ${K8S_MANIFESTS_DIR}/"
            }
        }
    }
}
