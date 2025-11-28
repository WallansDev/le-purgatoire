# Le Purgatoire

**Auteur :** Timothé VAQUIÉ (Wallans)  
**Version :** 1.0

## 🎯 Objectif

Application web de gestion d’interventions permettant de référencer:

-   Les entreprises prestataires
-   Les techniciens
-   Les interventions planifiées, en cours ou terminées

L’interface inclut un tableau de bord riche en statistiques, comme le top 10 des meilleurs techniciens, les retards, les interventions à finir et celles non notées.
<br><br>

## ✨ Fonctionnalités principales

-   **Référentiel entreprise & techniciens** : coordonnées, notation globale, notation ponctualité
-   **Gestion des interventions** : suivi complet (planification, statut, progression, notation)
-   **Dashboard** :
    -   Top 10 des meilleurs techniciens (Scoring basé sur le nombres d'interventions, et la ponctualité)
    -   Interventions à finaliser ou non démarrées
    -   Notation et feedback par intervention
-   **Statistiques avancées** : vues synthétiques pour aider à la priorisation et à la qualité de service
-   **Interface claire** : navigation structurée et accès rapide aux actions fréquentes
    <br><br>

## 🧱 Architecture & stack (suggestion)

-   Framework backend : Laravel 12 / PHP 8.2+
-   Base de données : MySQL (par défaut, facile à migrer vers PostgreSQL ou autre)
-   Build tool : Vite
-   Admin panel : Filament 4.2
-   Authentification : Laravel Breeze  
    <br>

## 🚀 Démarrage rapide

### Installation manuelle

```bash
# 1. Installer les dépendances
composer install
npm install

# 2. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 4. Créer la base de données MySQL (si elle n'existe pas)
--

# 4. Exécuter les migrations et seeders
php artisan migrate --seed

# 5. Démarrer les serveurs
npm run serve
# Ou séparément :
php artisan serve    #(dans un terminal)
npm run dev          #(dans un autre terminal)
```

L'application sera accessible sur **http://localhost:8000**

### Prérequis

-   **PHP** 8.2 ou supérieur
-   **Composer** (gestionnaire de dépendances PHP)
-   **Node.js** 18+ et **npm**
-   **MySQL** (ou autre)

## ✅ Tests unitaires

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
```

## 🧑‍💻 Contribution

1. Forker le dépôt
2. Créer une branche (`git checkout -b feature/ma-feature`)
3. Commiter (`git commit -m "Ajout ma feature"`)
4. Pousser (`git push origin feature/ma-feature`)
5. Ouvrir une Pull Request

## 📄 Licence

Pour l'instant <i>Le Purgatoire</i> est un projet en open-source. N'hésitez pas à y contribuer.
