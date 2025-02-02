 Snowtricks - Guide d'installation

Snowtricks est une plateforme collaborative destinée aux passionnés de snowboard. Ce guide vous expliquera comment installer et configurer le projet sur votre machine.

📌 Prérequis

Avant de commencer, assurez-vous d'avoir installé les éléments suivants :
PHP 8.1 ou supérieur
Composer
Symfony CLI
MySQL (ou une autre base de données supportée par Symfony)
Node.js et npm (pour la gestion des assets)

🔧 Étapes d'installation

1️⃣ Cloner le projet
git clone https://github.com/votre-utilisateur/snowtricks.git
cd snowtricks

2️⃣ Installer les dépendances
composer install
npm install

3️⃣ Configurer les variables d’environnement
Copiez le fichier .env et configurez votre base de données :
cp .env .env.local
Puis, modifiez la ligne DATABASE_URL dans .env.local en renseignant vos identifiants MySQL :
DATABASE_URL="mysql://utilisateur:motdepasse@127.0.0.1:3306/snowtricks?serverVersion=8.0"

4️⃣ Créer et remplir la base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load

5️⃣ Compiler les assets
npm run build

6️⃣ Lancer le serveur Symfony
symfony serve -d

Votre projet est maintenant accessible à l’adresse suivante :
👉 http://127.0.0.1:8000

🔐 Gestion des utilisateurs

Pour s'inscrire, rendez-vous sur /register.
Un email de confirmation sera envoyé (assurez-vous que le service d'email est bien configuré).
Les comptes administrateurs doivent être modifiés manuellement en base de données.
