pipeline {
    agent any
    
    environment {
        ECR_REGISTRY = 'tu-ecr-url.dkr.ecr.tu-region.amazonaws.com'
        ECR_REPO = 'tu-repositorio-ecr'
        TAG = "${env.BUILD_NUMBER}" // Cambia el tag en cada construcción
    }
    
    stages {
        stage('Pull del Repositorio') {
            steps {
                // Clona o hace pull de tu repositorio de código
                checkout scm
            }
        }
        
        stage('Pruebas Unitarias') {
            steps {
                // Ejecuta tus pruebas unitarias aquí
                // Ejemplo: phpunit
            }
        }
        
        stage('Construir y Publicar Imagen Docker') {
            steps {
                script {
                    // Construye la imagen Docker
                    docker.build("${ECR_REGISTRY}/${ECR_REPO}:${TAG}")
                    
                    // Autentica con ECR
                    withCredentials([[$class: 'AmazonWebServicesCredentialsBinding', accessKeyVariable: 'AWS_ACCESS_KEY_ID', credentialsId: 'tus-credenciales-de-aws', secretKeyVariable: 'AWS_SECRET_ACCESS_KEY']]) {
                        docker.withRegistry("${ECR_REGISTRY}", 'ecr') {
                            // Push de la imagen a ECR
                            docker.image("${ECR_REGISTRY}/${ECR_REPO}:${TAG}").push()
                            
                            // Push de la imagen como 'latest'
                            docker.image("${ECR_REGISTRY}/${ECR_REPO}:${TAG}").push('latest')
                        }
                    }
                }
            }
        }
        
        stage('Desplegar en Kubernetes') {
            steps {
                // Utiliza kubectl para aplicar tus recursos de Kubernetes (Deployment, Servicio, etc.)
                sh 'kubectl apply -f tus-manifestos-kubernetes/'
            }
        }
    }
}
