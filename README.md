Codacy:

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/77e5b47b3b2a45ecb7e630c5c162e02f)](https://app.codacy.com/gh/Gngoyi99/NGOYI_Germain_1_SnowTricks_git_092024/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)
# Snowtricks - Guide d'installation

## 📌 Prérequis

- **PHP** 8.1 ou supérieur  
- **Composer**  
- **Symfony CLI**  
- **MySQL** (ou une autre base de données supportée par Symfony)  
- **Node.js et npm** (pour la gestion des assets)  

## 🔧 Étapes d'installation

### 1️⃣ Cloner le projet
```sh
git clone https://github.com/Gngoyi99/NGOYI_Germain_1_SnowTricks_git_092024.git
cd snowtricks
```
## 2️⃣ Installer les dépendances
```sh
composer install
npm install
```
## 3️⃣ Configurer les variables d’environnement
```sh
cp .env .env.local
```
La connexion à la db(voir compose.yaml):
```sh
DATABASE_URL="postgresql://utilisateur:motdepasse@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
```
Voir le fichier Article.txt à la racine qui contient la requête des figures

## 4️⃣ Compiler les assets
```sh
npm run build
```
## 5️⃣ Lancer le serveur Symfony
```sh
symfony serve -d
```
