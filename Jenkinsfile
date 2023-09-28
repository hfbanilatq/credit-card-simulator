pipeline {
    agent any

    environment {
        ECR_REGISTRY = '309682544380.dkr.ecr.us-east-1.amazonaws.com'
        ECR_REPO = 'credit-card-simulator'
        K8S_MANIFESTS_DIR = 'kubernetes/Manifiesto.yml'
        APP_VERSION = "${env.BUILD_NUMBER}"
    }

    stages {
        stage('Pull del Repositorio') {
            steps {
                checkout scm
            }
        }

        stage('Reemplazar Imagen Tag') {
            steps {
                script {
                    def MAYOR = 1 + APP_VERSION.toInteger() / 100
                    def MINOR = (int) (APP_VERSION.toInteger() / 10) % 10
                    def DEPLOYMENT = APP_VERSION.toInteger() % 10
                    def CUSTOM_TAG = "${MAYOR}.${MINOR}.${DEPLOYMENT}"

                    // Asegúrate de que tu Dockerfile esté configurado correctamente para `containerd`
                    
                    // Construir la imagen con `ctr` (esto podría no ser necesario en Kubernetes)
                    sh "ctr -n=k8s.io images import . ${customImageTag}"

                    // Autenticarse con ECR
                    withCredentials([[$class: 'AmazonWebServicesCredentialsBinding', accessKeyVariable: 'AWS_ACCESS_KEY_ID', credentialsId: 'AwsCredentials', secretKeyVariable: 'AWS_SECRET_ACCESS_KEY']]) {
                        // Cambiar el tag de la imagen en ECR de 'latest' a la versión actual
                        sh "aws ecr get-login-password --region us-east-1 | docker login --username AWS --password-stdin ${ECR_REGISTRY}"
                        def MANIFEST = sh "aws ecr batch-get-image --repository-name ${ECR_REPO} --image-ids imageTag=latest --output text --query images[].imageManifest"
                        sh "aws ecr put-image --repository-name ${ECR_REPO} --image-tag ${CUSTOM_TAG} --image-manifest '${MANIFEST}'"
                        // Hacer push de la imagen personalizada
                        sh "aws ecr batch-delete-image --repository-name ${ECR_REPO} --image-ids imageTag=latest"
                    }
                }
            }
        }

        stage('Eliminar la antepenultima imagen') {
            steps {
                script {
                    withCredentials([[$class: 'AmazonWebServicesCredentialsBinding', accessKeyVariable: 'AWS_ACCESS_KEY_ID', credentialsId: 'AwsCredentials', secretKeyVariable: 'AWS_SECRET_ACCESS_KEY']]) {
                        def images = sh(script: "aws ecr describe-images --repository-name ${ECR_REPO} --query 'reverse(sort_by(imageDetails, &imagePushedAt))[].imageDigest'", returnStatus: true).trim()
                        def imageToDelete = sh(script: "echo '${images}' | sed -n '2p' | tr -d ','", returnStatus: true).trim()
                        sh "aws ecr batch-delete-image --repository-name ${ECR_REPO} --image-ids imageDigest='${imageToDelete}'"
                    }
                }
            }
        }

        stage('Construir imagen y enviar al repositorio') {
            steps {
                sh "docker build -t ${ECR_REGISTRY}/${ECR_REPO}:latest ."
                sh "docker tag  ${ECR_REGISTRY}/${ECR_REPO}:latest ${ECR_REPO}:latest"
                sh "docker push  ${ECR_REGISTRY}/${ECR_REPO}:latest"
                sh "docker push  ${ECR_REGISTRY}/${ECR_REPO}:latest"
            }
        }

        stage('Desplegar en Kubernetes') {
            steps {
                sh "kubectl --kubeconfig=${Kubernetes} apply -f ${K8S_MANIFESTS_DIR}/"
            }
        }
    }
}
