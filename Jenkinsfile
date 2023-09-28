/* groovylint-disable LineLength, NestedBlockDepth */
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
        stage('Instalar dependencias') {
            steps {
                sh 'npm install'
                sh 'composer install'
            }
        }
        stage('Publicar resultados') {
            steps {
                // Publicar los resultados de las pruebas unitarias y análisis estático en Jenkins
                junit '**/junit-*.xml'
                publishHTML(target: [
                    allowMissing: false,
                    alwaysLinkToLastBuild: true,
                    keepAll: true,
                    reportDir: 'storage/reports',
                    reportFiles: 'phpstan-report.html',
                    reportName: 'Análisis Estático'
                ])
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
                        sh "aws ecr get-login-password --region us-east-1 | docker login --username AWS --password-stdin ${ECR_REGISTRY}"
                        String manifest = sh "aws ecr batch-get-image --repository-name ${ECR_REPO} --image-ids imageTag=latest --output text --query images[].imageManifest"
                        sh "aws ecr put-image --repository-name ${ECR_REPO} --image-tag ${customTag} --image-manifest '${manifest}'"
                        sh "aws ecr batch-delete-image --repository-name ${ECR_REPO} --image-ids imageTag=latest"
                    }
                }
            }
        }

        stage('Eliminar la antepenultima imagen') {
            steps {
                script {
                    withCredentials([[$class: 'AmazonWebServicesCredentialsBinding', accessKeyVariable: 'AWS_ACCESS_KEY_ID', credentialsId: 'AwsCredentials', secretKeyVariable: 'AWS_SECRET_ACCESS_KEY']]) {
                        String images = sh(script: "aws ecr describe-images --repository-name ${ECR_REPO} --query 'reverse(sort_by(imageDetails, &imagePushedAt))[].imageDigest'", returnStatus: true).trim()
                        String imageToDelete = sh(script: "echo '${images}' | sed -n '2p' | tr -d ','", returnStatus: true).trim()
                        sh "aws ecr batch-delete-image --repository-name ${ECR_REPO} --image-ids imageDigest='${imageToDelete}'"
                    }
                }
            }
        }

        stage('Construir imagen y enviar al repositorio') {
            steps {
                script {
                    docker.withRegistry("https://${ECR_REGISTRY}", "ecr:us-east-1:${AWS_CREDENTIALS_ID}") {
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
