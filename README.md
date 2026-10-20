# TaskManager

Application PHP/MySQL utilisée pour apprendre Terraform, Ansible et Jenkins avec une VM locale VirtualBox.

## Fichiers d'infrastructure

- `Jenkinsfile` : vérifie PHP/PHPStan/PHPUnit, Terraform et Ansible, puis déploie sur Ubuntu si toutes les validations réussissent. Il ne crée ni ne détruit de VM. Il interroge GitHub toutes les deux minutes environ pour détecter un push.
- `terraform/main.tf` : déclare une VM Ubuntu Server avec un réseau NAT et un réseau privé VirtualBox.
- `terraform/variables.tf` : définit et valide le nom, les ressources et l'interface privée de la VM.
- `terraform/outputs.tf` : affiche l'adresse IP privée à reporter dans Ansible.
- `terraform/terraform.tfvars.example` : exemple de configuration locale ; sa copie `terraform.tfvars` n'est pas versionnée.
- `ansible/inventory.ini` : indique l'hôte Ubuntu, l'utilisateur SSH et la clé de laboratoire.
- `ansible/site.yml` : applique le rôle de déploiement sur la VM.
- `ansible/requirements.yml` : déclare la collection Ansible nécessaire aux modules MariaDB.
- `ansible/roles/taskmanager/defaults/main.yml` : valeurs non secrètes du rôle.
- `ansible/roles/taskmanager/tasks/main.yml` : installe Apache/PHP/MariaDB, prépare la base et déploie l'application.
- `ansible/roles/taskmanager/handlers/main.yml` : redémarre Apache quand sa configuration change.
- `ansible/roles/taskmanager/templates/apache-env.conf.j2` : transmet à PHP les paramètres de connexion MySQL.
- `config/database.php` : lit les paramètres DB depuis l'environnement tout en gardant les défauts locaux XAMPP.
- `.gitignore` : exclut l'état Terraform et les paramètres locaux, qui ne doivent pas être partagés.

Le fournisseur Terraform VirtualBox choisi est expérimental et ancien. Ces fichiers sont destinés à un laboratoire local, pas à un environnement de production.

## Lancer le laboratoire

### 1. Préparer VirtualBox (à faire une fois)

Sur Windows, installez Terraform et VirtualBox. Dans VirtualBox, créez un réseau **Host-only** avec DHCP activé. Ajoutez aussi une deuxième carte réseau Host-only à la VM Kali et sélectionnez ce même réseau. Il faudra que Kali et Ubuntu puissent communiquer sur ce réseau.

### 2. Créer la VM avec Terraform (PowerShell sur Windows)

Depuis la racine du dépôt :

```powershell
Copy-Item terraform\terraform.tfvars.example terraform\terraform.tfvars
```

Modifiez `terraform\terraform.tfvars` pour que `hostonly_interface` corresponde exactement au nom de l'interface Host-only affiché par VirtualBox, puis exécutez :

```powershell
Set-Location terraform
terraform init
terraform fmt -check
terraform validate
terraform plan
terraform apply
terraform output -raw taskmanager_ip
```

`terraform apply` crée la VM et télécharge son image. Confirmez l'opération uniquement après avoir vérifié le plan. Notez l'adresse IP affichée par la dernière commande.

### 3. Configurer Ansible depuis Kali

Installez Ansible, Python et Git sur Kali, puis depuis la racine du dépôt :

```bash
sudo apt update
sudo apt install -y ansible git python3-pymysql
ansible-galaxy collection install -r ansible/requirements.yml
```

Récupérez le dépôt dans Kali. Dans `ansible/inventory.ini`, remplacez l'adresse d'exemple par l'adresse retournée par Terraform. Le fichier utilise la clé SSH de laboratoire Vagrant. Sur Kali, récupérez-la et protégez-la ainsi :

```bash
mkdir -p ~/.ssh
curl -fsSL https://raw.githubusercontent.com/hashicorp/vagrant/main/keys/vagrant -o ~/.ssh/vagrant_insecure_private_key
chmod 600 ~/.ssh/vagrant_insecure_private_key
```

Cette clé privée Vagrant est connue publiquement : elle est **uniquement pour le laboratoire**.

Générez un mot de passe dédié (par exemple `openssl rand -hex 24`), puis depuis la racine du dépôt, lancez :

```bash
ansible-playbook -i ansible/inventory.ini ansible/site.yml
```

Le playbook demande ce mot de passe de base de données sans l'afficher. Une fois terminé, ouvrez `http://<adresse-IP>/taskmanager/public/`.

### 4. Jenkins (facultatif)

Le `Jenkinsfile` attend un agent Linux avec PHP 8.1+ et les extensions CLI usuelles, MariaDB/MySQL, curl, Terraform, Ansible et Git. Sur Kali, installez les dépendances et préparez une base réservée aux tests, distincte de celle d'Ubuntu :

```bash
sudo apt install -y php-cli php-curl php-mbstring php-mysql php-xml mariadb-server
sudo systemctl enable --now mariadb
sudo mariadb < database/schema.sql
```

Puis créez un utilisateur qui n'a accès qu'à cette base (remplacez le mot de passe) :

```sql
CREATE USER 'taskmanager_ci'@'127.0.0.1' IDENTIFIED BY 'MOT_DE_PASSE_HEXADECIMAL';
GRANT ALL PRIVILEGES ON taskmanager.* TO 'taskmanager_ci'@'127.0.0.1';
```

Dans Jenkins, créez le credential **Username with password** `taskmanager-ci-db` avec cet utilisateur et ce mot de passe. Cette base locale est uniquement pour PHPUnit ; ne réutilisez pas la base d'Ubuntu.

Avant d'activer le déploiement, créez aussi ces credentials Jenkins :

- `taskmanager-ubuntu-ssh` : type **SSH Username with private key**, utilisateur `vagrant` et clé privée de la VM. La clé Vagrant fournie est publiquement connue et ne doit être utilisée que sur ce réseau de laboratoire.
- `taskmanager-ubuntu-db-password` : type **Secret text**, mot de passe MySQL exact défini lors du premier déploiement Ansible. Il ne faut pas le confondre avec le credential de test PHPUnit.

Le service Jenkins doit avoir les outils requis (`git`, `php`, `curl`, `terraform`, `ansible-playbook`, `ansible-galaxy`), pouvoir se connecter à MariaDB sur Kali et joindre Ubuntu sur `192.168.56.102` en SSH. Vérifiez l'empreinte SSH de l'hôte Ubuntu et ajoutez-la au `known_hosts` du compte système `jenkins` avant le premier build. Si l'adresse DHCP d'Ubuntu change, mettez à jour `TASKMANAGER_HOST` dans `Jenkinsfile`.

Configurez le job en **Pipeline script from SCM**, avec l'URL GitHub, la branche à surveiller et `Jenkinsfile` comme chemin. Le pipeline interroge périodiquement le dépôt GitHub (environ toutes les deux minutes) ; il n'exige donc pas de rendre Jenkins accessible depuis Internet pour recevoir un webhook. Après un push détecté, il lance les validations et ne déploie sur Ubuntu par Ansible que si elles réussissent. Le déploiement n'appelle pas Terraform. Ne lancez pas ce Jenkinsfile sur un agent Windows : ses étapes utilisent des commandes Linux.

Pour supprimer la VM de laboratoire, retournez dans `terraform/` et lancez `terraform destroy` ; cela supprime la VM et toutes les données qu'elle contient.

## Configuration locale de la base

`config/database.php` lit `DB_HOST`, `DB_NAME`, `DB_USER` et `DB_PASSWORD` dans l'environnement. Si ces variables ne sont pas définies, les valeurs locales XAMPP (`localhost`, `taskmanager`, `root`, mot de passe vide) sont conservées. Importez `database/schema.sql` dans MySQL avant les tests.
