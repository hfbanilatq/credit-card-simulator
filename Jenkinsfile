/* groovylint-disable LineLength, NestedBlockDepth */
pipeline {
    agent {
        kubernetes {
            yamlFile 'kubernetes/agent.yml'
        }
    }

    environment {
        ECR_REGISTRY = '309682544380.dkr.ecr.us-east-1.amazonaws.com'
        ECR_REPO = 'credit-card-simulator'
        K8S_MANIFESTS_DIR = 'kubernetes/Manifiesto.yaml'
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
                container('jenkins-agent') {
                    sh 'php --version'
                    sh 'composer install'
                    sh 'composer --version'
                    sh 'cp .env.example .env'
                    sh 'php artisan key:generate'
                }
            }
        }

        stage('Ejecutar pruebas unitarias y de integracion') {
            steps {
                container('jenkins-agent') {
                    sh 'php artisan test'
                    sh 'vendor/bin/phpstan analyze' 
                }
            }
        }

        stage('Reemplazar Imagen Tag') {
            steps {
                container('jenkins-agent') {
                    script {
                        def int mayor = 1 + APP_VERSION.toInteger() / 100
                        def int minor = (int) (APP_VERSION.toInteger() / 10) % 10
                        def int deployment = APP_VERSION.toInteger() % 10
                        def String customTag = "${mayor}.${minor}.${deployment}"

                        withCredentials([[$class: 'AmazonWebServicesCredentialsBinding', accessKeyVariable: 'AWS_ACCESS_KEY_ID', credentialsId: 'AWS_CREDENTIALS', secretKeyVariable: 'AWS_SECRET_ACCESS_KEY']]) {
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
        }

        stage('Eliminar la antepenultima imagen') {
            steps {
                container('jenkins-agent') {
                    script {
                        withCredentials([[$class: 'AmazonWebServicesCredentialsBinding', accessKeyVariable: 'AWS_ACCESS_KEY_ID', credentialsId: 'AWS_CREDENTIALS', secretKeyVariable: 'AWS_SECRET_ACCESS_KEY']]) {
                            String images = ""
                            images = sh(script: "aws ecr describe-images --repository-name ${ECR_REPO} --query 'reverse(sort_by(imageDetails, &imagePushedAt))[].imageDigest'", returnStdout: true)
                            String imageToDelete = sh(script: "echo '${images}' | sed -n '3p' | tr -d ','",  returnStdout: true)

                            if (imageToDelete && !imageToDelete.isEmpty() && imageToDelete.contains("sha256")) {
                                sh "aws ecr batch-delete-image --repository-name ${ECR_REPO} --image-ids imageDigest='${imageToDelete}'"
                            } else {
                                echo 'No se encontraron imágenes para eliminar.'
                            }
                        }
                    }
                }
            }
        }

        stage('Construir imagen y enviar al repositorio') {
            steps {
                container('dind') {
                    script {
                        docker.build("${ECR_REGISTRY}/${ECR_REPO}:latest", '.')
                        docker.withRegistry("https://${ECR_REGISTRY}", 'ecr:us-east-1:' + 'AWS_CREDENTIALS') {
                            
                            docker.image("${ECR_REGISTRY}/${ECR_REPO}:latest").push()
                        }
                    }
                }
            }
        }

        stage('Desplegar en Kubernetes') {
            steps {
                container('jenkins-agent') {
                    script {
                        withCredentials([[$class: 'AmazonWebServicesCredentialsBinding', accessKeyVariable: 'AWS_ACCESS_KEY_ID', credentialsId: 'AWS_CREDENTIALS', secretKeyVariable: 'AWS_SECRET_ACCESS_KEY']]) {
                            sh "aws eks update-kubeconfig --name jenkins-docker --alias jenkis-docker"
                            sh "kubectl apply -f ${K8S_MANIFESTS_DIR}"
                        }
                    }
                }
            }
        }
    }
}
