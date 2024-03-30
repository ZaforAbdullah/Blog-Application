## Install DIND with sysbox
- Build Jenkins Docker image with Compose: 
    ```bash
        docker build -t jenkins-with-compose .    
    ```
- Install Sysbox: 
    ```bash
        sudo apt-get install ./sysbox-ce_0.6.3-0.linux_amd64.deb`
    ```
- Check Sysbox service status: 
    ```bash
        sudo systemctl status sysbox -n20
    ```
- Get Docker runtime information: 
    ```bash
        $ docker info | grep -i runtime
            Runtimes: io.containerd.runc.v2 runc sysbox-runc
            Default Runtime: runc
    ```
- Enable Sysbox service: 
    ```bash
        sudo systemctl enable sysbox
    ```
- Run Jenkins Docker container with Sysbox: 
    ```bash
        docker run -d --runtime=sysbox-runc -v jenkins_home:/var/jenkins_home -p 8080:8080 -p 50000:50000 --restart=on-failure jenkins-docker
    ```
- Retrieve initial admin password for Jenkins: 
    ```bash
        cat /var/jenkins_home/secrets/initialAdminPassword
    ```