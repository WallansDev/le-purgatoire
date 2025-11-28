# Le Purgatoire

**Author:** Timothé VAQUIÉ (Wallans)  
**Version:** 1.0

<br>

### 🌐 Read in other languages

[**Français**](README.fr.md)
<br><br>

## 🎯 Objective

Web application for managing interventions, allowing you to reference:

-   Service providers
-   Technicians
-   Planned, ongoing, or completed interventi# 2. Configurer l'environnement
    cp .env.example .env # Configurer le .env
    php artisan key:generate

# 4. Exécuter les migrations et seeders

php artisan migrate --seed

# 5. Démarrer les serveurs

npm run serve

# Ou séparément :

php artisan serve #(dans un terminal)
npm run dev #(dans un autre terminal)

````

L'application sera accessible sur **http://localhost:8000**

### Prérequis

-   **PHP** 8.2 ou supérieur
-   **Composer** (gestionnaire de dépendances PHP)
-   **Node.js** 18+ et **npm**
-   **MySQL** (ou autre)

## ✅ Tests unitaires

```bash
php artisan test
````

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
```

## 🧑‍💻 Contribution

1. Forker le dépôt
2. Créer une branche (`git checkout -b feature/ma-feature`)
3. Commiter (`git commit -m "Ajout ma feature"`)
4. Pousser (`git push origin feature/ma-feature`)
5. Ouvrir une Pull Request

## 📄 Licence

Pour l'instant <i>Le Purgatoire</i> est un projet en open-source. N'hésitez pas à y contribuer.
ons

The interface includes a dashboard rich in statistics, such as the top 10 best technicians, delays, interventions to be completed, and those not yet rated.
<br><br>

## ✨ Main features

-   **Company & technician database**: contact details, overall rating, punctuality rating
-   **Service call management**: comprehensive tracking (scheduling, status, progress, rating)
-   **Dashboard**:
    -   Top 10 best technicians (scoring based on number of jobs and punctuality)
    -   Jobs to be completed or not started
    -   Rating and feedback per job
-   **Advanced statistics**: summary views to help with prioritization and service quality
-   **Clear interface**: structured navigation and quick access to frequent actions
    <br><br>

## 🧱 Architecture & stack (suggestion)

-   Backend framework: Laravel 12 / PHP 8.2+
-   Database: MySQL (default, easy to migrate to PostgreSQL or other)
-   Build tool: Vite
-   Admin panel: Filament 4.2
-   Authentication: Laravel Breeze  
     <br>

## 🚀 Quick start

### Manual installation

```bash
# 1. Install dependencies
composer install
npm install

# 2. Configure the environment
cp .env.example .env # Configure .env
php artisan key:generate

# 4. Run migrations and seeders
php artisan migrate --seed

# 5. Start the servers
npm run serve
# Or separately:
php artisan serve    #(in a terminal)
npm run dev          # (in another terminal)
```

The application will be accessible at **http://localhost:8000**

### Prerequisites

-   **PHP** 8.2 or higher
-   **Composer** (PHP dependency manager)
-   **Node.js** 18+ and **npm**
-   **MySQL** (or other)

## ✅ Unit tests

```bash
php artisan test
```

## 📝 Useful commands

```bash
# Rebuild frontend assets
npm run build

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Recreate database
php artisan migrate:fresh --seed
```

## 🧑‍💻 Contribution

1. Fork the repository
2. Create a branch (`git checkout -b feature/my-feature`)
3. Commit (`git commit -m “Add my feature”`)
4. Push (`git push origin feature/my-feature`)
5. Open a Pull Request

## 📄 License

For now, <i>Le Purgatoire</i> is an open-source project. Feel free to contribute.
