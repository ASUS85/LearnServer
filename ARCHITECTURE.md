# LearnServer - Architecture MVC Restructurée

## 📋 Vue d'ensemble

LearnServer a été complètement restructuré en architecture MVC propre et professionnelle, tout en conservant exactement le fonctionnement actuel du projet.

## 📁 Structure du projet

```
LearnServer/
├── public/                 # Point d'entrée publique
│   ├── index.php          # Front controller
│   ├── .htaccess          # Réécriture d'URLs
│   ├── css/               # Sera servi depuis assets/css/
│   └── js/                # Sera servi depuis assets/js/
│
├── app/                    # Code applicatif
│   ├── config/
│   │   └── database.php    # Configuration DB centralisée
│   ├── core/              # Classes core (extensible)
│   ├── controllers/       # Contrôleurs (Auth, Admin, etc)
│   │   ├── AuthController.php
│   │   ├── AdminController.php
│   │   ├── StudentController.php
│   │   └── TeacherController.php
│   ├── models/            # Modèles de données
│   │   ├── User.php
│   │   ├── Student.php
│   │   ├── Teacher.php
│   │   └── Admin.php
│   ├── views/             # Vues (templates HTML)
│   │   ├── auth/
│   │   │   ├── login.php
│   │   │   └── register.php
│   │   ├── admin/
│   │   │   ├── dashboard.php
│   │   │   ├── students.php
│   │   │   ├── teachers.php
│   │   │   ├── subjects.php
│   │   │   ├── schedule.php
│   │   │   ├── notes.php
│   │   │   └── settings.php
│   │   ├── student/
│   │   │   └── dashboard.php
│   │   ├── teacher/
│   │   │   └── dashboard.php
│   │   └── layouts/
│   │       └── admin_sidebar.php
│   ├── middleware/        # Middlewares
│   │   └── AuthMiddleware.php
│   └── helpers/           # Fonctions utilitaires
│       └── helpers.php
│
├── assets/                 # Assets (CSS, JS) - Actuels
│   ├── css/
│   └── js/
│
├── config/                 # [DEPRECIÉ] - Utiliser app/config/
├── admin/                  # [DEPRECIÉ] - Utiliser app/views/admin/
├── student/                # [DEPRECIÉ] - Utiliser app/views/student/
├── teacher/                # [DEPRECIÉ] - Utiliser app/views/teacher/
│
├── .htaccess              # Réécriture d'URLs racine
└── README.md              # Ce fichier
```

## 🚀 Démarrage

### 1. Configuration

Tout est automatiquement configuré. Les fichiers principaux sont:
- **Database**: `app/config/database.php`
- **Point d'entrée**: `public/index.php`

### 2. URLs de l'application

L'application fonctionne avec des URLs propres:

```
http://localhost/LearnServer/                    # Page de connexion
http://localhost/LearnServer/register            # Inscription
http://localhost/LearnServer/admin/dashboard     # Dashboard admin
http://localhost/LearnServer/student/dashboard   # Dashboard étudiant
http://localhost/LearnServer/teacher/dashboard   # Dashboard enseignant
```

### 3. Authentification

Le système d'authentification fonctionne comme avant:
- 3 rôles: `admin`, `student`, `teacher`
- Sessions utilisateur automatiquement gérées
- Middleware d'authentification sur les routes protégées

### 4. Accès à l'application

```
URL Racine: http://localhost/LearnServer/
→ Redirects automatiquement vers public/index.php
```

## 🔧 Architecture MVC

### Models (app/models/)
Gèrent la logique métier et l'accès aux données:
- `User` - Classe abstraite parent
- `Student` - Gestion des étudiants
- `Teacher` - Gestion des enseignants
- `Admin` - Gestion des administrateurs

### Controllers (app/controllers/)
Gèrent les requêtes et la logique applicative:
- `AuthController` - Login, Register, Logout
- `AdminController` - Gestion admin complète
- `StudentController` - Dashboard étudiant
- `TeacherController` - Dashboard enseignant

### Views (app/views/)
Templates HTML pour le rendu:
- `auth/` - Pages d'authentification
- `admin/` - Pages de gestion
- `student/` - Portail étudiant
- `teacher/` - Portail enseignant
- `layouts/` - Layouts réutilisables

### Helpers (app/helpers/)
Fonctions utilitaires globales:
- `basePath()` - Chemin racine
- `baseUrl()` - URL de base
- `view()` - Rendre une vue
- `redirect()` - Redirection
- `assetUrl()` - URLs des assets
- `isAuthenticated()`, `hasRole()` - Vérification auth
- `sanitize()`, `hashPassword()`, `verifyPassword()` - Sécurité

### Middleware (app/middleware/)
Traitement des requêtes:
- `AuthMiddleware` - Vérification authentification/permissions

## 🔐 Sécurité

### Passwords
- Hachage: `password_hash()` avec `PASSWORD_DEFAULT`
- Vérification: `password_verify()`
- Migration: Les anciens mots de passe non hachés doivent être rémigrés

### CSRF Protection (À implémenter)
Les fonctions sont en place: `generateToken()`, `verifyToken()`, `csrfField()`

### Validation
- `sanitize()` - Nettoyage des entrées
- `isValidEmail()` - Validation emails
- Validation côté serveur sur tous les contrôleurs

## 🔄 Routes de l'application

Le routage se fait via le front controller `public/index.php`:

```php
// Routes publiques
GET  /index → Affiche login
GET  /register → Affiche inscription
POST /login → Traite le login
POST /do-register → Traite l'inscription
GET  /logout → Déconnexion

// Routes Admin (authentification requise)
GET  /admin/dashboard → Dashboard
GET  /admin/students → Gestion étudiants
GET  /admin/teachers → Gestion enseignants
GET  /admin/subjects → Gestion matières
GET  /admin/schedule → Emploi du temps
GET  /admin/notes → Gestion notes
GET  /admin/settings → Paramètres

// Routes Student
GET  /student/dashboard → Dashboard étudiant

// Routes Teacher
GET  /teacher/dashboard → Dashboard enseignant
```

## 📝 Bonnes pratiques

### 1. Ajouter une nouvelle page

**1. Créer la vue** (`app/views/admin/new_page.php`)
```php
<?php $var = $var ?? ''; ?>
<!-- HTML -->
```

**2. Ajouter la route** (dans `public/index.php`)
```php
case 'new_page':
    $admin->newPage();
    break;
```

**3. Créer le contrôleur** (dans `AdminController.php`)
```php
public function newPage() {
    $data = /* ... */;
    view('admin.new_page', ['var' => $data]);
}
```

### 2. Utiliser les helpers

```php
// Redirection
redirect('admin/dashboard');

// Rendu de vue
view('admin.students', ['students' => $data]);

// URLs
echo assetUrl('css/style.css');
echo baseUrl('admin/students');

// Authentification
if (!isAuthenticated()) { /* ... */ }
if (hasRole('admin')) { /* ... */ }
```

### 3. Utiliser les modèles

```php
$student = new Student($pdo);
$students = $student->getAllWithDetails();
$student->create(['nom' => 'Dupont', /* ... */]);
$student->update($id, ['email' => 'new@example.com']);
```

## 🛠️ Maintenance

### Fichiers à supprimer (ancienne structure)
Les fichiers suivants sont maintenant remplacés par la nouvelle architecture:

```
api/login.php          → app/controllers/AuthController.php
api/logout.php         → app/controllers/AuthController.php
api/register.api       → Inutilisé
index.php              → public/index.php
register.php           → app/views/auth/register.php
admin/*.php            → app/views/admin/*.php
student/*.php          → app/views/student/*.php
teacher/*.php          → app/views/teacher/*.php
config/database.php    → app/config/database.php
```

**À conserver temporairement**: Dossier `assets/` pour les fichiers CSS/JS

### Copie des assets

Pour une structure complète, copiez les assets:
```bash
cp -r assets/css/* public/css/
cp -r assets/js/* public/js/
```

Ou modifiez `.htaccess` pour pointer vers les nouveaux emplacements.

## 📊 Sessions utilisateur

Les données de session disponibles après login:

```php
$_SESSION['user_id']        // ID de l'utilisateur
$_SESSION['user_role']      // Rôle (admin, student, teacher)
$_SESSION['user_nom']       // Nom
$_SESSION['user_prenom']    // Prénom
$_SESSION['user_email']     // Email
$_SESSION['user_matricule'] // Matricule (si applicable)
```

Accédez via le helper:
```php
$user = currentUser();
echo $user['role'];  // admin
```

## 🐛 Dépannage

### Erreur 404
- Vérifier que `.htaccess` est activé (mod_rewrite)
- Vérifier la URL de base dans `helpers.php` → `baseUrl()`

### Erreur authentification
- Vérifier que la base de données est connectée
- Vérifier les tables: `admins`, `etudiants`, `enseignants`

### Assets non chargés
- Les assets sont servis via `/LearnServer/assets/`
- Vérifier les chemins dans `helpers.php` → `assetUrl()`

## 🚀 Prochaines étapes

1. ✅ Architecture MVC complète
2. ✅ Routage propre et centralisé
3. ✅ Helpers et utilities
4. ✅ Authentification et middleware
5. ⏳ Implémenter la protection CSRF
6. ⏳ Ajouter la pagination
7. ⏳ Implémenter la cache
8. ⏳ Ajouter des tests unitaires
9. ⏳ Documenter l'API (si JSON endpoints)

## 📞 Support

Pour toute question sur la nouvelle architecture MVC, consultez:
- Les commentaires dans les fichiers PHP
- La structure des dossiers
- Les helpers dans `app/helpers/helpers.php`

---

**Dernière mise à jour**: 2026-05-19  
**Version**: 2.0 (Architecture MVC)  
**Statut**: Fonctionnel ✅
