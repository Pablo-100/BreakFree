# 🌿 BreakFree — Plateforme de Suivi de Sevrage

**BreakFree** est une plateforme SaaS de suivi et motivation pour le sevrage d'addictions légales (tabac, vape, alcool, etc.). Elle offre un tableau de bord personnalisé, un journal quotidien, des statistiques visuelles et un système de gamification pour accompagner la démarche de chaque utilisateur.

---

## 📋 Table des matières

- [Fonctionnalités](#-fonctionnalités)
- [Stack technique](#-stack-technique)
- [Architecture du projet](#-architecture-du-projet)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Base de données (Neon)](#-base-de-données-neon)
- [Déploiement](#-déploiement)
- [Sécurité](#-sécurité)
- [API interne](#-api-interne)
- [Comptes par défaut](#-comptes-par-défaut)

---

## ✨ Fonctionnalités

### Authentification & Profil
- Inscription / connexion sécurisée (bcrypt)
- Réinitialisation de mot de passe avec token
- Protection anti-brute-force (rate limiting)
- Profil personnalisable (type d'addiction, objectif, coût journalier)

### Tableau de bord
- Streak (jours consécutifs sans consommation)
- Argent économisé (calcul automatique)
- Pourcentage de progression vers l'objectif
- Moyenne hebdomadaire + tendance (amélioration / stable / dégradation)
- Graphiques interactifs (consommation & envies sur 30 jours)
- Système de badges et niveaux

### Journal quotidien
- Saisie quotidienne : quantité, niveau d'envie (0-10), humeur, notes
- Historique avec pagination
- Modification et suppression d'entrées
- Système upsert (une seule entrée par jour)

### Gamification
- **9 badges** : Premier pas, 1 semaine, 1 mois, 100 jours, 1 an...
- **9 niveaux** : Débutant → Légende (avec couleurs distinctives)
- Déblocage automatique en fonction du streak

### Administration
- Tableau de bord admin avec statistiques globales
- Gestion des utilisateurs (liste, suppression)
- Répartition par type d'addiction
- Graphiques de consommation globale

---

## 🛠️ Stack technique

| Couche | Technologie |
|--------|------------|
| **Frontend** | HTML5, CSS3 (custom dark theme), JavaScript vanilla |
| **Backend** | PHP 8+ (architecture MVC custom) |
| **Base de données** | PostgreSQL (Neon serverless) |
| **Graphiques** | Chart.js 4.4.1 (CDN) |
| **Sécurité** | bcrypt, CSRF tokens, XSS escaping, rate limiting |

---

## 📁 Architecture du projet

```
BreakFree/
├── config/
│   ├── app.php              # Configuration (addictions, humeurs, badges, niveaux)
│   ├── database.php         # Connexion PDO singleton (Neon/PostgreSQL)
│   ├── env.php              # Chargement du fichier .env
│   ├── helpers.php          # Fonctions utilitaires (CSRF, auth, flash...)
│   └── Router.php           # Routeur MVC avec paramètres dynamiques
├── controllers/
│   ├── AdminController.php  # Dashboard admin & gestion utilisateurs
│   ├── AuthController.php   # Inscription, connexion, mot de passe oublié
│   ├── DailyLogController.php # Journal quotidien CRUD
│   ├── DashboardController.php # Tableau de bord + API graphiques
│   └── ProfileController.php   # Profil utilisateur
├── database/
│   └── schema.sql           # Script de migration PostgreSQL
├── models/
│   ├── DailyLog.php         # Entrées quotidiennes (upsert, stats, graphiques)
│   ├── LoginAttempt.php     # Tentatives de connexion (rate limiting)
│   ├── User.php             # Utilisateurs (CRUD, auth, reset)
│   └── UserBadge.php        # Badges & niveaux (gamification)
├── public/
│   ├── css/style.css        # Thème dark complet (~1000 lignes)
│   ├── js/app.js            # Sidebar, Chart.js, interactions
│   ├── index.php            # Point d'entrée unique (front controller)
│   └── .htaccess            # Réécriture Apache
├── routes/
│   └── web.php              # Définition de toutes les routes
├── views/
│   ├── admin/
│   │   ├── index.php        # Dashboard admin
│   │   └── users.php        # Gestion des utilisateurs
│   ├── auth/
│   │   ├── forgot-password.php
│   │   ├── login.php
│   │   ├── register.php
│   │   └── reset-password.php
│   ├── dailylog/
│   │   ├── edit.php         # Modification d'une entrée
│   │   ├── history.php      # Historique avec pagination
│   │   └── index.php        # Formulaire du jour
│   ├── dashboard/
│   │   └── index.php        # Tableau de bord principal
│   ├── layouts/
│   │   ├── 404.php          # Page non trouvée
│   │   ├── 500.php          # Erreur serveur
│   │   └── app.php          # Layout principal (sidebar + content)
│   └── profile/
│       └── index.php        # Édition du profil
├── .env                     # Variables d'environnement (à configurer)
├── .gitignore
└── README.md
```

---

## 🚀 Installation

### Prérequis

- **PHP 8.0+** avec extensions : `pdo`, `pdo_pgsql`, `mbstring`, `openssl`
- **Apache** avec `mod_rewrite` activé (ou Nginx avec config équivalente)
- **Composer** (optionnel, pas de dépendances externes)
- **Neon** (compte gratuit sur [neon.tech](https://neon.tech))

### Étapes

1. **Cloner le projet**
   ```bash
   git clone <repo-url> BreakFree
   cd BreakFree
   ```

2. **Configurer le serveur web**
   - Pointer le DocumentRoot vers le dossier `public/`
   - S'assurer que `mod_rewrite` est activé

3. **Configurer les variables d'environnement**
   - Copier et modifier le fichier `.env` (voir section Configuration)

4. **Créer la base de données**
   - Se connecter à Neon et exécuter `database/schema.sql`

5. **Tester**
   - Accéder à `http://localhost/` (ou votre domaine)
   - Se connecter avec le compte admin par défaut

---

## ⚙️ Configuration

### Fichier `.env`

```env
# Base de données (Neon PostgreSQL)
DB_HOST=ep-xxxxx.region.aws.neon.tech
DB_PORT=5432
DB_NAME=breakfree
DB_USER=breakfree_owner
DB_PASS=votre_mot_de_passe
DB_SSLMODE=require

# Application
APP_NAME=BreakFree
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com

# Mail (pour réinitialisation de mot de passe)
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USER=noreply@votre-domaine.com
MAIL_PASS=mot_de_passe_mail
MAIL_FROM=noreply@votre-domaine.com
MAIL_FROM_NAME=BreakFree
```

### Variables importantes

| Variable | Description | Valeur par défaut |
|----------|------------|-------------------|
| `APP_ENV` | Environnement (`development` / `production`) | `production` |
| `APP_DEBUG` | Afficher les erreurs détaillées | `false` |
| `DB_SSLMODE` | Mode SSL pour Neon | `require` |

---

## 🗄️ Base de données (Neon)

### Création du projet Neon

1. Créer un compte sur [neon.tech](https://neon.tech)
2. Créer un nouveau projet (région au choix)
3. Copier les identifiants de connexion dans `.env`

### Exécution du schéma

Via la **SQL Console** de Neon ou avec `psql` :

```bash
psql "postgresql://user:password@ep-xxxxx.region.aws.neon.tech/breakfree?sslmode=require" -f database/schema.sql
```

Le script `schema.sql` crée automatiquement :
- L'extension `pgcrypto` (pour les UUID)
- Les tables : `users`, `daily_logs`, `login_attempts`, `user_badges`
- Les index et contraintes
- Le trigger `updated_at`
- Le compte admin par défaut

---

## 🌐 Déploiement

### Apache (recommandé)

VirtualHost de base :

```apache
<VirtualHost *:443>
    ServerName breakfree.votre-domaine.com
    DocumentRoot /var/www/BreakFree/public

    <Directory /var/www/BreakFree/public>
        AllowOverride All
        Require all granted
    </Directory>

    SSLEngine on
    SSLCertificateFile /path/to/cert.pem
    SSLCertificateKeyFile /path/to/key.pem
</VirtualHost>
```

### Nginx (alternative)

```nginx
server {
    listen 443 ssl;
    server_name breakfree.votre-domaine.com;
    root /var/www/BreakFree/public;
    index index.php;

    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Checklist de production

- [ ] `APP_ENV=production` et `APP_DEBUG=false`
- [ ] SSL/TLS activé
- [ ] Droits fichiers corrects (`chmod 600 .env`)
- [ ] `database/schema.sql` exécuté
- [ ] Tester la connexion BD
- [ ] Changer le mot de passe admin par défaut

---

## 🔒 Sécurité

| Mesure | Implémentation |
|--------|---------------|
| **Hachage mots de passe** | bcrypt (cost 12) |
| **Protection CSRF** | Token aléatoire par session |
| **Protection XSS** | `htmlspecialchars()` systématique |
| **Injection SQL** | Requêtes préparées PDO exclusivement |
| **Rate limiting** | Max 5 tentatives / 15 min par IP |
| **En-têtes de sécurité** | X-Content-Type-Options, X-Frame-Options, Referrer-Policy |
| **Sessions** | httponly, secure, samesite=strict |
| **Énumération emails** | Messages génériques sur reset password |

---

## 📡 API interne

Endpoints REST internes utilisés par Chart.js (requièrent une session authentifiée) :

| Méthode | Route | Description |
|---------|-------|-------------|
| GET | `/api/chart/consumption` | Données de consommation (30 jours) |
| GET | `/api/chart/cravings` | Données d'envies (30 jours) |
| GET | `/api/stats` | Statistiques utilisateur |
| GET | `/api/admin/stats` | Statistiques globales (admin) |

Réponse JSON type :

```json
{
  "labels": ["2025-01-01", "2025-01-02", "..."],
  "data": [5, 3, 2, 1, 0, 0, 1]
}
```

---

## 👤 Comptes par défaut

| Rôle | Email | Mot de passe |
|------|-------|-------------|
| **Admin** | `admin@breakfree.app` | `Admin123!` |

> ⚠️ **Changez immédiatement le mot de passe admin en production !**

---

## 📄 Licence

Projet privé — Tous droits réservés.

---

Développé avec ❤️ pour aider chacun à briser ses chaînes.
