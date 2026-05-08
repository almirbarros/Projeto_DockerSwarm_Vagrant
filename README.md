# 🚀 Projeto Docker Swarm Vagrant

## 🛠️ Stack Tecnológica
- Linguagem: PHP (versão X.X)
- Banco de Dados: MySQL
- Ambiente de Desenvolvimento: Vagrant (VirtualBox)
- Orquestração e Containers: Docker & Docker Swarm

## 🏗️ Arquitetura da Infra
O projeto utiliza um fluxo de trabalho moderno para garantir paridade entre ambientes:Vagrant: Provisiona máquinas virtuais locais que simulam nós de um cluster real.Docker: Empacota a aplicação PHP e o banco MySQL em containers leves.Docker Swarm: Gerencia o cluster, garantindo alta disponibilidade e escalonamento dos serviços.

## 🚀 Como Executar1. 
Preparar o Ambiente (Vagrant)Suba as VMs que servirão como nós do cluster:bashvagrant up
Use o código com cuidado.2. Inicializar o Cluster (Swarm)Acesse a máquina principal e inicialize o Swarm:bashvagrant ssh manager
docker swarm init --advertise-addr <IP_DA_VM>
Use o código com cuidado.3. Deploy da StackCom o cluster ativo, suba os serviços (PHP + MySQL):bashdocker stack deploy -c docker-compose.yml meu_projeto
Use o código com cuidado.

## 📋 Variáveis de Ambiente
Certifique-se de configurar o arquivo .env para a conexão do MySQL:MYSQL_ROOT_PASSWORDMYSQL_DATABASEDB_HOST (no Swarm, use o nome do serviço definido no compose)
