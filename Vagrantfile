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
        vb.name = name
        vb.cpus = "1"
        vb.gui = false
      end

      machine.vm.provision "shell", path: "instalar-docker.sh"
      machine.vm.provision "shell", inline: "sudo usermod -aG docker vagrant"
	
      if name == "master"
        machine.vm.provision "shell", inline: <<-SHELL
          docker swarm init --advertise-addr 192.168.1.#{conf["ip"]}
          docker network create --driver overlay rede-app
          docker volume create db_data
          docker volume create app_php
		  
          if [ -f /vagrant/index.php ]; then
            docker run --rm -v app_php:/v -v /vagrant:/src alpine sh -c "cp /src/index.php /v/index.php && chown -R 33:33 /v"
          fi

          docker service create --name mysql-db --detach=true --network rede-app \
            --constraint 'node.role == manager' --replicas 1 -p 3306:3306 \
            -e MYSQL_ROOT_PASSWORD=Senha123 -e MYSQL_DATABASE=meubanco \
            --mount type=volume,src=db_data,dst=/var/lib/mysql \
            --mount type=bind,src=/vagrant/banco.sql,dst=/docker-entrypoint-initdb.d/banco.sql \
            mysql:8.0

          docker service create --name meu-app-php --detach=true --network rede-app \
            --replicas 4 -p 80:80 \
            --mount type=volume,src=app_php,dst=/var/www/html \
            php:apache \
            sh -c "docker-php-ext-install mysqli && apache2-foreground"
					
          docker swarm join-token worker -q > /vagrant/swarm_token.txt
          echo "docker swarm join --token $(cat /vagrant/swarm_token.txt) 192.168.1.#{conf['ip']}:2377" > /vagrant/join_cluster.sh
          chmod +x /vagrant/join_cluster.sh
        SHELL
      else
        machine.vm.provision "shell", inline: <<-SHELL
          echo "Configurando nó WORKER: #{name}..."
          while [ ! -f /vagrant/join_cluster.sh ]; do sleep 5; done
          sudo sh /vagrant/join_cluster.sh
        SHELL
      end
    end
  end

  # --- VERIFICADOR FINAL (Roda apenas no master após tudo ser criado) ---
  config.vm.define "master" do |master|
    master.vm.provision "shell", inline: <<-SHELL
      echo "Finalizando: Aguardando disponibilidade do Cluster e Serviços..."
	  until curl -s --head --fail http://192.168.1.50/index.php > /dev/null; do
          printf '.'
          sleep 5
      done
      echo -e "\n\n"
      echo "=========================================================="
      echo "  TUDO PRONTO! O cluster e os workers estão ativos."
      echo "  Acesse o site em: http://192.168.1.50"
      echo "  Serviços rodando em 4 réplicas (balanceadas)."
      echo "=========================================================="
    SHELL
  end
end