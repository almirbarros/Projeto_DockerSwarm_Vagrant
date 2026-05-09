# 🚀 Cluster Docker Swarm Automático com Vagrant

![Ubuntu](https://shields.io)
![Docker](https://shields.io)
![Swarm](https://shields.io)
![Vagrant](https://shields.io)


Este projeto provisiona automaticamente um cluster Docker Swarm com dois nós utilizando Vagrant e VirtualBox. O nó principal (master) configura os serviços de banco de dados e aplicação assim que sobe.

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
   vagrant up
   ```

2. **O que acontece nos bastidores:**
   - O Vagrant cria as VMs e instala o Docker via `instalar-docker.sh`.
   - O `master` inicializa o Swarm e cria os volumes.
   - O `master` gera um script `join_cluster.sh` na pasta raiz.
   - O `node01` aguarda esse script e entra no cluster automaticamente.
   - O serviço PHP é escalado para **4 réplicas** distribuídas entre os nós.

---

## 📁 Arquivos Gerados (Ignorados no Git)

Após o `vagrant up`, os seguintes arquivos de controle serão criados na raiz:
- `swarm_token.txt`: Token puro para novos workers.
- `join_cluster.sh`: Comando completo para novos nós entrarem no cluster.

---

## 🔍 Verificando o Cluster

Para verificar se tudo subiu corretamente, acesse o master:
```bash
vagrant ssh master
docker node ls
docker service ls
```
