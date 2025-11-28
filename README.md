# Le Purgatoire

**Auteur :** Timothé VAQUIÉ (Wallans)  
**Version :** 1.0

## 🎯 Objectif

Application web de gestion d’interventions permettant de référencer:

-   Les entreprises clientes
-   Les techniciens
-   Les interventions planifiées, en cours ou terminées

L’interface inclut un tableau de bord riche en statistiques, comme le top 10 des meilleurs techniciens, les retards, les interventions à finir et celles non notées.

## ✨ Fonctionnalités principales

-   **Référentiel entreprise & techniciens** : fiches détaillées, coordonnées, niveau de compétence
-   **Gestion des interventions** : suivi complet (planification, statut, progression, notation)
-   **Dashboard** :
    -   Top 10 des meilleurs techniciens
    -   Suivi des retards
    -   Interventions à finaliser ou non démarrées
    -   Notation et feedback par intervention
-   **Statistiques avancées** : vues synthétiques pour aider à la priorisation et à la qualité de service
-   **Interface claire** : navigation structurée et accès rapide aux actions fréquentes

## 🧱 Architecture & Stack technique

-   **Framework backend** : Laravel 12 / PHP 8.2+
-   **Base de données** : SQLite (par défaut, facile à migrer vers MySQL/PostgreSQL)
-   **Frontend** : Blade Templates / Tailwind CSS
-   **Build tool** : Vite
-   **Admin panel** : Filament 4.2
-   **Authentification** : Laravel Breeze

## 🚀 Démarrage rapide

### Installation automatique (Recommandé)

#### Windows

```bash
# Double-cliquez sur setup.bat ou exécutez dans PowerShell :
.\setup.bat

# Puis pour démarrer l'application :
.\start.bat
```

#### Linux / macOS

```bash
# Rendre les scripts exécutables (première fois seulement)
chmod +x setup.sh start.sh

# Installer l'application
./setup.sh

# Puis pour démarrer l'application :
./start.sh
```

### Installation manuelle

Si vous préférez installer manuellement :

```bash
# 1. Installer les dépendances
composer install
npm install

# 2. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 3. Créer la base de données SQLite (si elle n'existe pas)
touch database/database.sqlite

# 4. Exécuter les migrations et seeders
php artisan migrate --seed

# 5. Démarrer les serveurs
npm run serve
# Ou séparément :
# php artisan serve    (dans un terminal)
# npm run dev          (dans un autre terminal)
```

L'application sera accessible sur **http://localhost:8000**

### Prérequis

-   **PHP** 8.2 ou supérieur
-   **Composer** (gestionnaire de dépendances PHP)
-   **Node.js** 18+ et **npm**
-   **SQLite** (inclus avec PHP, aucune configuration nécessaire)

### 📋 Que font les scripts d'automatisation ?

Les scripts `setup.bat` / `setup.sh` effectuent automatiquement :

1. ✅ Vérification des prérequis (PHP, Composer, Node.js)
2. ✅ Installation des dépendances PHP (`composer install`)
3. ✅ Installation des dépendances Node.js (`npm install`)
4. ✅ Création du fichier `.env` depuis `.env.example`
5. ✅ Génération de la clé d'application Laravel
6. ✅ Création de la base de données SQLite
7. ✅ Exécution des migrations et seeders

Les scripts `start.bat` / `start.sh` lancent simplement l'application avec les deux serveurs (Laravel + Vite) en parallèle.

## ✅ Tests

```bash
php artisan test
```

## 📝 Commandes utiles

```bash
# Rebuild des assets frontend
npm run build

# Nettoyer le cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Recréer la base de données
php artisan migrate:fresh --seed

# Accéder à Tinker (console Laravel)
php artisan tinker
```

## 🧑‍💻 Contribution

1. Forker le dépôt
2. Créer une branche (`git checkout -b feature/ma-feature`)
3. Commiter (`git commit -m "Ajout ma feature"`)
4. Pousser (`git push origin feature/ma-feature`)
5. Ouvrir une Pull Request

## 📄 Licence

_(À compléter selon le choix de licence : MIT, GPL, etc.)_

---

Besoin d’ajouter des captures d’écran, une démo ou un badge de CI ? N’hésite pas à demander !
