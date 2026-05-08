machines = {
  "master" => {"memory" => "512","ip" => "50"},
  "node01" => {"memory" => "512","ip" => "51"}
}

Vagrant.configure("2") do |config|
  config.vm.box = "bento/ubuntu-24.04"
  
  machines.each do |name, conf|
    config.vm.define "#{name}" do |machine|
      machine.vm.hostname = name
      machine.vm.network "public_network", ip: "192.168.1.#{conf["ip"]}"
      machine.vm.boot_timeout = 600
      
      machine.vm.provider "virtualbox" do |vb|
	    vb.name = name # Nome exato no VirtualBox
        vb.cpus = "1"
        vb.gui = false
      end

      # SCRIPT instalacao Docker
      machine.vm.provision "shell", path: "instalar-docker.sh"
      
      # Permite usar docker sem sudo (Importante para os comandos abaixo)
      machine.vm.provision "shell", inline: "sudo usermod -aG docker vagrant"

      if name == "master"
        machine.vm.provision "shell", inline: <<-SHELL
          # Inicializa o Swarm
          docker swarm init --advertise-addr 192.168.1.#{conf["ip"]}
          
		  # Cria os volumes
          docker volume create db_data
          docker volume create app_php
		  
		  # Copia o seu index.php para o volume
          if [ -f /vagrant/index.php ]; then
            docker run --rm -v app_php:/v -v /vagrant:/src alpine cp /src/index.php /v/index.php
          fi

          # Serviço MySQL - Adicionado --detach=true
          docker service create --name mysql-db \
            --detach=true \
            --constraint 'node.role == manager' \
            --replicas 1 -p 3306:3306 \
            -e MYSQL_ROOT_PASSWORD=Senha123 -e MYSQL_DATABASE=meubanco \
            --mount type=volume,src=db_data,dst=/var/lib/mysql \
            --mount type=bind,src=/vagrant/banco.sql,dst=/docker-entrypoint-initdb.d/banco.sql \
            mysql:8.0

          # Serviço PHP - Adicionado --detach=true
          docker service create --name meu-app-php \
            --detach=true \
            --replicas 4 -p 80:80 \
            --mount type=volume,src=app_php,dst=/var/www/html \
            php:apache
			
		  # Gera o token para o worker
          docker swarm join-token worker -q > /vagrant/swarm_token.txt
          echo "docker swarm join --token $(cat /vagrant/swarm_token.txt) 192.168.1.#{conf['ip']}:2377" > /vagrant/join_cluster.sh
          chmod +x /vagrant/join_cluster.sh
        SHELL

	  else
        # CONFIGURAÇÃO DOS WORKERS (node01, node02, etc)
        machine.vm.provision "shell", inline: <<-SHELL
          echo "Configurando nó WORKER: #{name}..."
          echo "Aguardando o master gerar o token..."
          while [ ! -f /vagrant/join_cluster.sh ]; do sleep 5; done
          sudo sh /vagrant/join_cluster.sh
        SHELL
      end
    end
  end
end