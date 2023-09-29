/* groovylint-disable LineLength, NestedBlockDepth */
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
        stage('Instalar dependencias y construir') {
            steps {
                sh 'php --version'
                sh 'composer install'
                sh 'composer --version'
                sh 'cp .env.example .env'
                sh 'php artisan key:generate'
            }
        }

        stage('Reemplazar Imagen Tag') {
            steps {
                script {
                    def int mayor = 1 + APP_VERSION.toInteger() / 100
                    def int minor = (int) (APP_VERSION.toInteger() / 10) % 10
                    def int deployment = APP_VERSION.toInteger() % 10
                    def String customTag = "${mayor}.${minor}.${deployment}"

                    withCredentials([[$class: 'AmazonWebServicesCredentialsBinding', accessKeyVariable: 'AWS_ACCESS_KEY_ID', credentialsId: 'AwsCredentials', secretKeyVariable: 'AWS_SECRET_ACCESS_KEY']]) {
                        def manifest = ""
                        manifest = sh(script: "aws ecr batch-get-image --repository-name ${ECR_REPO} --image-ids imageTag=latest --output text --query images[].imageManifest", returnStdout: true).trim()
                        
                        if(manifest && !manifest.isEmpty()) {
                            sh "aws ecr put-image --repository-name ${ECR_REPO} --image-tag ${customTag} --image-manifest '${manifest}'"
                            sh "aws ecr batch-delete-image --repository-name ${ECR_REPO} --image-ids imageTag=latest"
                        } else {
                            echo 'No existe el tag latest'
                        }
                    }
                }
            }
        }

        stage('Eliminar la antepenultima imagen') {
            steps {
                script {
                    withCredentials([[$class: 'AmazonWebServicesCredentialsBinding', accessKeyVariable: 'AWS_ACCESS_KEY_ID', credentialsId: 'AwsCredentials', secretKeyVariable: 'AWS_SECRET_ACCESS_KEY']]) {
                        String images = ""
                        images = sh(script: "aws ecr describe-images --repository-name ${ECR_REPO} --query 'reverse(sort_by(imageDetails, &imagePushedAt))[].imageDigest'", returnStatus: true)
                        String imageToDelete = sh(script: "echo '${images}' | sed -n '2p' | tr -d ','", returnStatus: true)

                        if (imageToDelete && !imageToDelete.isEmpty()) {
                            sh "aws ecr batch-delete-image --repository-name ${ECR_REPO} --image-ids imageDigest='${imageToDelete}'"
                        } else {
                            echo 'No se encontraron imágenes para eliminar.'
                        }
                    }
                }
            }
        }

        stage('Construir imagen y enviar al repositorio') {
            steps {
                script {
                    docker.withRegistry("https://${ECR_REGISTRY}", 'ecr:us-east-1:AKIAUQGUET36PUFCKQVW') {
                        docker.build("${ECR_REGISTRY}/${ECR_REPO}:latest", '.')
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
