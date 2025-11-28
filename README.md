# Le Purgatoire

**Author:** Timothé VAQUIÉ (Wallans)  
**Version:** 1.0

<br>

### 🌐 Read in other languages

[**Français**](README.fr.md)
<br><br>

## 🎯 Objective

Web application for managing interventions that allows tracking:

-   Service provider companies
-   Technicians
-   Planned, in progress, or completed interventions

The interface includes a dashboard rich in statistics, such as the top 10 best technicians, delays, interventions to complete, and those not yet rated.
<br><br>

## ✨ Main Features

-   **Company & technician directory** : contact information, overall rating, punctuality rating
-   **Intervention management** : complete tracking (planning, status, progress, rating)
-   **Dashboard** :
    -   Top 10 best technicians (Scoring based on number of interventions and punctuality)
    -   Interventions to finalize or not yet started
    -   Rating and feedback per intervention
-   **Advanced statistics** : summary views to help with prioritization and service quality
-   **Clear interface** : structured navigation and quick access to frequent actions
    <br><br>

## 🧱 Architecture & Stack

-   Backend framework : Laravel 12 / PHP 8.2+
-   Database : MySQL (default, easy to migrate to PostgreSQL or others)
-   Build tool : Vite
-   Admin panel : Filament 4.2
-   Authentication : Laravel Breeze  
    <br>

## 🚀 Quick Start

### Manual Installation

```bash
# 1. Install dependencies
composer install
npm install

# 2. Configure environment
cp .env.example .env # Configure the .env file
php artisan key:generate

# 4. Run migrations and seeders
php artisan migrate --seed

# 5. Start servers
npm run serve
# Or separately:
php artisan serve    #(in one terminal)
npm run dev          #(in another terminal)
```

The application will be accessible at **http://localhost:8000**

### Prerequisites

-   **PHP** 8.2 or higher
-   **Composer** (PHP dependency manager)
-   **Node.js** 18+ and **npm**
-   **MySQL** (or other)

## ✅ Unit Tests

```bash
php artisan test
```

## 📝 Useful Commands

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

## 🧑‍💻 Contributing

1. Fork the repository
2. Create a branch (`git checkout -b feature/my-feature`)
3. Commit (`git commit -m "Add my feature"`)
4. Push (`git push origin feature/my-feature`)
5. Open a Pull Request

## 📄 License

For now, <i>Le Purgatoire</i> is an open-source project. Feel free to contribute.
