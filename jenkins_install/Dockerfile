FROM jenkins/jenkins:lts

USER root

# Instala herramientas necesarias
RUN apt-get update && apt-get install -y \
    docker.io \
    awscli

# Instala kubectl
RUN curl -LO "https://dl.k8s.io/release/$(curl -L -s https://dl.k8s.io/release/stable.txt)/bin/linux/amd64/kubectl" && \
    chmod +x kubectl && \
    mv kubectl /usr/local/bin/

# Inicia el servicio Docker (debes asegurarte de que el usuario "jenkins" tenga permisos para hacerlo)
RUN service docker start
RUN update-rc.d docker defaults
RUN systemctl enable docker



# Agrega al usuario "jenkins" al grupo "docker"
RUN usermod -aG docker jenkins

USER jenkins
