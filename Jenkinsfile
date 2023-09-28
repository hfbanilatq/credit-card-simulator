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

                    withCredentials([[$class: 'AmazonWebServicesCredentialsBinding', accessKeyVariable: 'AWS_ACCESS_KEY_ID', credentialsId: 'AwsCredentials', secretKeyVariable: 'AWS_SECRET_ACCESS_KEY']]) {
 
                        // Importar las clases necesarias del SDK de AWS para Java
                        def AmazonECRClient = new AmazonECRClient()
                        AmazonECRClient.setRegion(Regions.US_EAST_1) // Reemplaza con tu región

                        // Obtener el manifiesto de la última imagen en el repositorio
                        def latestImageTag = "latest" // Etiqueta de la última imagen
                        def getBatchImageRequest = new BatchCheckLayerAvailabilityRequest()
                                .withRepositoryName(ECR_REPO)
                                .withImageIds(new ImageIdentifier().withImageTag(latestImageTag))
                        def layerAvailabilityResponse = AmazonECRClient.batchCheckLayerAvailability(getBatchImageRequest)
                        def imageDigest = layerAvailabilityResponse.getLayers().first().getLayerDigest()

                        // Crear una nueva etiqueta para la imagen personalizada
                        def putImageRequest = new PutImageRequest()
                                .withRepositoryName(ECR_REPO)
                                .withImageTag(CUSTOM_TAG)
                                .withImageManifest(layerAvailabilityResponse.getLayerDigests().first())
                        AmazonECRClient.putImage(putImageRequest)

                        // Eliminar la etiqueta "latest" de la imagen
                        def deleteImageRequest = new BatchDeleteImageRequest()
                                .withRepositoryName(ECR_REPO)
                                .withImageIds(new ImageIdentifier().withImageTag(latestImageTag))
                        AmazonECRClient.batchDeleteImage(deleteImageRequest)
                    }
                }
            }
        }

        stage('Eliminar la imagen mas antigua') {
            steps {
                script {
                    withCredentials([[$class: 'AmazonWebServicesCredentialsBinding', accessKeyVariable: 'AWS_ACCESS_KEY_ID', credentialsId: 'AwsCredentials', secretKeyVariable: 'AWS_SECRET_ACCESS_KEY']]) {

                        def AmazonECRClient = new AmazonECRClient()
                        AmazonECRClient.setRegion(Regions.US_EAST_1) 

                        def listImagesRequest = new ListImagesRequest()
                                .withRepositoryName(ECR_REPO)
                                .withMaxResults(1000) 
                        def listImagesResponse = AmazonECRClient.listImages(listImagesRequest)

                        def sortedImages = listImagesResponse.getImageIds().sort { a, b ->
                            b.getImagePushedAt().compareTo(a.getImagePushedAt())
                        }

                        def antepenultimateImage = sortedImages[sortedImages.size()-1]

                        def batchDeleteRequest = new BatchDeleteImageRequest()
                                .withRepositoryName(ECR_REPO)
                                .withImageIds(antepenultimateImage)
                        AmazonECRClient.batchDeleteImage(batchDeleteRequest)
                    }
                }
            }
        }

        stage('Construir imagen y enviar al repositorio') {
            steps {
                docker.build("${ECR_REGISTRY}/${ECR_REPO}:${CUSTOM_TAG}", ".")
                docker.image("${ECR_REGISTRY}/${ECR_REPO}:${CUSTOM_TAG}").push()
            }
        }

        stage('Desplegar en Kubernetes') {
            steps {
                sh "kubectl --kubeconfig=${Kubernetes} apply -f ${K8S_MANIFESTS_DIR}/"
            }
        }
    }
}
