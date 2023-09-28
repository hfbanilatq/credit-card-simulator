pipeline {
    agent {
        kubernetes {
            label 'my-k8s-agent-label' // Etiqueta que coincide con la configuración de tu nube de Kubernetes
            defaultContainer 'jnlp' // Nombre del contenedor JNLP
        }
    }

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

        stage('Reemplazar Imagen Tag') {
            steps {
                script {
                    // Construir la imagen Containerd (esto podría no ser necesario en Kubernetes)
                    def MAYOR = 1 + ${APP_VERSION} / 100
                    def MINOR = (${APP_VERSION} / 10) % 10
                    def DEPLOYMENT = ${APP_VERSION} % 10
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
                withCredentials([[$class: 'AmazonWebServicesCredentialsBinding', accessKeyVariable: 'AWS_ACCESS_KEY_ID', credentialsId: 'AwsCredentials', secretKeyVariable: 'AWS_SECRET_ACCESS_KEY']]) {
                def images = sh "aws ecr describe-images --repository-name ${ECR_REPO} --query 'reverse(sort_by(imageDetails, &imagePushedAt))[].imageDigest'"
                def imageToDelete = sh "$(echo '${images}' | sed -n '2p' | tr -d ',')"
                sh "aws ecr batch-delete-image --repository-name ${ECR_REPO} --image-ids imageDigest='${imageToDelete}'"
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
                def kubeconfigCred = credentials('Kubernetes')
                sh "kubectl --kubeconfig=${kubeconfigCred} apply -f ${K8S_MANIFESTS_DIR}/"
            }
        }
    }
}
