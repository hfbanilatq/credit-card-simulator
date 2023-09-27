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
RUN sudo systemctl enable docker
RUN sudo chkconfig docker on
RUN sudo usermod -aG docker jenkins

USER jenkins
