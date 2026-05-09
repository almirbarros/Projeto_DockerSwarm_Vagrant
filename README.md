# 🚀 Cluster Docker Swarm Automático com Vagrant

![Ubuntu](https://img.shields.io/badge/-Ubuntu-E95420?style=flat&logo=ubuntu&logoColor=white)
![Docker](https://img.shields.io/badge/-Docker-2496ED?style=flat&logo=docker&logoColor=white)
![Swarm](https://img.shields.io/badge/-Swarm-FFA633?style=flat&logo=swarm&logoColor=white)
![Vagrant](https://img.shields.io/badge/-Vagrant-1868F2?style=flat&logo=vagrant&logoColor=white)
![PHP](https://img.shields.io/badge/-PHP-777BB4?style=flat&logo=php&logoColor=white)
![MYSQL](https://img.shields.io/badge/-MySQL-4479A1?style=flat&logo=mysql&logoColor=white)

<!--  Link dos icones -->
<!-- https://gist.github.com/kimjisub/360ea6fc43b82baaf7193175fd12d2f7#file-gistfile1-txt-L1 -->

> [!NOTE]
> Este cluster é para ambiente de desenvolvimento.

---
## 🏗️ Estrutura das Máquinas (Vagrant)

| Máquina | Função | IP Estático | Memória | CPU |
| :--- | :--- | :--- | :--- | :--- |
| `master` | Manager / DB / App | `192.168.1.50` | 512MB | 1 |
| `node01` | Worker / App | `192.168.1.51` | 512MB | 1 |

---

## 🛠️ Stack e Serviços Autodeploy

A configuração do Vagrant já realiza o deploy automático dos seguintes serviços:


| Serviço | Imagem | Réplicas | Portas | Volumes / Persistência |
| :--- | :--- | :---: | :--- | :--- |
| `mysql-db` | `mysql:8.0` | `1` | `3306:3306` | `db_data` + `banco.sql` |
| `meu-app-php`| `php:apache`| `4` | `80:80` | `app_php` (index.php) |

---

## 🚀 Como Executar

O projeto é **Totalmente Automatizado**. Você só precisa de um comando:

1. **Subir o Cluster:**
   ```bash
   	vagrant up --provider virtualbox
   ```

2. **O que acontece nos bastidores:**
   - O Vagrant cria as VMs e instala o Docker via `instalar-docker.sh`.
   - O `master` inicializa o Swarm e cria os volumes.
   - O `master` gera um script `join_cluster.sh` na pasta raiz.
   - O `node01` aguarda esse script e entra no cluster automaticamente.
   - O serviço PHP é escalado para **4 réplicas** distribuídas entre os nós.

> [!TIP]
> Use o comando `docker service ls` para verificar se as 4 réplicas já subiram.

3. **Destruir:**
   ```bash
   	vagrant destroy -f
   ```
> [!NOTE]
>  Se nao conseguir destruir
>  ```bash
>	 stop-process -name ruby -force
>  ```

4. **Limpeza mais profunda:**
   ```bash
   	vagrant box prune
   
	   %USERPROFILE%\VirtualBox VMs
	   %USERPROFILE%\.vagrant.d\boxes
   ```
---

## 📁 Arquivos Gerados (Ignorados no Git)

Após o `vagrant up`, os seguintes arquivos de controle serão criados na raiz:
- `swarm_token.txt`: Token puro para novos workers.
- `join_cluster.sh`: Comando completo para novos nós entrarem no cluster.

> [!IMPORTANT]
> O Vagrantfile gera o arquivo `join_cluster.sh` automaticamente.

> [!CAUTION]
> Não apague o arquivo `swarm_token.txt` enquanto o cluster estiver rodando.
Este projeto provisiona automaticamente um cluster Docker Swarm com dois nós utilizando Vagrant e VirtualBox. O nó principal (master) configura os serviços de banco de dados e aplicação assim que sobe.
---

## 🗄️ Estrutura do Banco de Dados
Este script realiza a configuração automática do ambiente, garantindo a criação do banco de dados e a definição da estrutura da tabela.

-- Cria o banco de dados caso não exista
```bash
CREATE DATABASE IF NOT EXISTS meubanco;
USE meubanco;
```

-- Estrutura da tabela 'dados'
```bash
CREATE TABLE dados (
    id INT,
    data1 VARCHAR(50),
    data2 VARCHAR(50),
    hostname VARCHAR(50),
    ip VARCHAR(50)
);
```

## 🔍 Verificando o Cluster

Para verificar se tudo subiu corretamente, acesse o master:
Vai até o diretório onde está o script

```bash
vagrant ssh master
sudo su
```

```bash
docker node ls
docker service ls
```
