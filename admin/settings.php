<?php

session_start();

if(
    !isset($_SESSION['user_role']) ||
    $_SESSION['user_role'] != 'admin'
){

    header("Location: ../index.php");
    exit;

}

require_once("../config/database.php");

/* =========================================================
   VARIABLES
========================================================= */

$message = "";
$error = "";

$admin_id = $_SESSION['user_id'];

/* =========================================================
   RECUPERATION ADMIN
========================================================= */

$stmt = $pdo->prepare("
    SELECT *
    FROM admins
    WHERE id = ?
");

$stmt->execute([$admin_id]);

$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$admin){

    session_destroy();

    header("Location: ../index.php");
    exit;

}

$adminColumns = [];

try {
    $columnsStmt = $pdo->query("SHOW COLUMNS FROM admins");
    foreach ($columnsStmt->fetchAll(PDO::FETCH_ASSOC) as $column) {
        if (isset($column['Field'])) {
            $adminColumns[$column['Field']] = true;
        }
    }
} catch (PDOException $e) {
    // En cas d'erreur metadata, on garde un fallback minimal.
    $adminColumns = [
        'nom' => true,
        'email' => true,
        'mot_de_passe' => true
    ];
}

/* =========================================================
   SECURITE ANTI BRUTE FORCE
========================================================= */

if(!isset($_SESSION['login_attempts'])){

    $_SESSION['login_attempts'] = 0;

}

if(!isset($_SESSION['last_attempt'])){

    $_SESSION['last_attempt'] = time();

}

if(
    $_SESSION['login_attempts'] >= 5 &&
    (time() - $_SESSION['last_attempt']) < 300
){

    die("Trop de tentatives. Réessayez dans 5 minutes.");

}

/* =========================================================
   UPDATE PROFIL
========================================================= */

if(isset($_POST['save_profile']) || isset($_POST['update_profile'])){

    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $theme = trim($_POST['theme'] ?? '');

    if(
        empty($nom) ||
        empty($email)
    ){

        $error = "Veuillez remplir tous les champs obligatoires.";

    }elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)){

        $error = "Email invalide.";

    }else{

        $check = $pdo->prepare("
            SELECT id
            FROM admins
            WHERE email = ?
            AND id != ?
        ");

        $check->execute([$email,$admin_id]);

        if($check->rowCount() > 0){

            $error = "Cet email existe déjà.";

        }else{

            /* =========================================
               UPLOAD PHOTO
            ========================================= */

            $photo_name = $admin['photo'] ?? null;

            if(
                isset($_FILES['photo']) &&
                $_FILES['photo']['error'] == 0
            ){

                $allowed = [

                    'image/jpeg',
                    'image/png',
                    'image/webp'

                ];

                if(
                    in_array(
                        $_FILES['photo']['type'],
                        $allowed
                    )
                ){

                    $extension = pathinfo(
                        $_FILES['photo']['name'],
                        PATHINFO_EXTENSION
                    );

                    $photo_name =
                        "admin_" .
                        time() .
                        "." .
                        $extension;

                    move_uploaded_file(

                        $_FILES['photo']['tmp_name'],

                        "../uploads/profiles/" .
                        $photo_name

                    );

                }

            }

            $setClauses = [];
            $values = [];

            if (isset($adminColumns['nom'])) {
                $setClauses[] = "nom = ?";
                $values[] = htmlspecialchars($nom);
            }

            if (isset($adminColumns['prenom'])) {
                $setClauses[] = "prenom = ?";
                $values[] = htmlspecialchars($prenom);
            }

            if (isset($adminColumns['email'])) {
                $setClauses[] = "email = ?";
                $values[] = htmlspecialchars($email);
            }

            if (isset($adminColumns['telephone'])) {
                $setClauses[] = "telephone = ?";
                $values[] = htmlspecialchars($telephone);
            }

            if (isset($adminColumns['photo'])) {
                $setClauses[] = "photo = ?";
                $values[] = $photo_name;
            }

            if (isset($adminColumns['theme'])) {
                $setClauses[] = "theme = ?";
                $values[] = $theme;
            }

            if (!empty($setClauses)) {
                $values[] = $admin_id;

                $update = $pdo->prepare("
                    UPDATE admins
                    SET " . implode(', ', $setClauses) . "
                    WHERE id = ?
                ");

                $update->execute($values);
            }

            $_SESSION['user_nom'] = $nom;
            $_SESSION['user_prenom'] = $prenom;
            $_SESSION['user_email'] = $email;

            $admin['nom'] = $nom;
            $admin['prenom'] = $prenom;
            $admin['email'] = $email;
            $admin['telephone'] = $telephone;
            $admin['photo'] = $photo_name;
            $admin['theme'] = $theme;

            $message = "Profil mis à jour avec succès.";

        }

    }

}

/* =========================================================
   CHANGEMENT MOT DE PASSE
========================================================= */

if(isset($_POST['change_password']) || isset($_POST['update_password'])){

    $current_password = $_POST['current_password'];

    $new_password = $_POST['new_password'];

    $confirm_password = $_POST['confirm_password'];

    if(
        empty($current_password) ||
        empty($new_password) ||
        empty($confirm_password)
    ){

        $error = "Veuillez remplir tous les champs.";

    }elseif(
        !password_verify($current_password, $admin['mot_de_passe'] ?? '') &&
        $current_password !== ($admin['mot_de_passe'] ?? '')
    ){

        $_SESSION['login_attempts']++;

        $_SESSION['last_attempt'] = time();

        $error = "Mot de passe actuel incorrect.";

    }elseif(strlen($new_password) < 6){

        $error = "Le mot de passe doit contenir au moins 6 caractères.";

    }elseif($new_password != $confirm_password){

        $error = "Les mots de passe ne correspondent pas.";

    }else{

        $_SESSION['login_attempts'] = 0;

        $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

        $updatePassword = $pdo->prepare("
            UPDATE admins
            SET mot_de_passe = ?
            WHERE id = ?
        ");

        $updatePassword->execute([

            $hashedPassword,
            $admin_id

        ]);

        $admin['mot_de_passe'] = $hashedPassword;

        $message = "Mot de passe modifié avec succès.";

    }

}

/* =========================================================
   DOUBLE AUTHENTIFICATION
========================================================= */

if(isset($_POST['toggle_2fa'])){

    $twofa = isset($_POST['twofa']) ? 1 : 0;

    $update2fa = $pdo->prepare("
        UPDATE admins
        SET two_factor_enabled = ?
        WHERE id = ?
    ");

    $update2fa->execute([

        $twofa,
        $admin_id

    ]);

    $message = "Paramètre double authentification mis à jour.";

}

/* =========================================================
   NOTIFICATIONS ADMIN
========================================================= */

if(isset($_POST['save_notifications'])){

    $email_notifications =
        isset($_POST['email_notifications']) ? 1 : 0;

    $security_alerts =
        isset($_POST['security_alerts']) ? 1 : 0;

    $system_updates =
        isset($_POST['system_updates']) ? 1 : 0;

    $notif = $pdo->prepare("
        UPDATE admins
        SET
            email_notifications = ?,
            security_alerts = ?,
            system_updates = ?
        WHERE id = ?
    ");

    $notif->execute([

        $email_notifications,
        $security_alerts,
        $system_updates,
        $admin_id

    ]);

    $message = "Préférences notifications enregistrées.";

}

/* =========================================================
   ACTIVITE CONNEXION / SESSIONS ACTIVES
========================================================= */

$activities = [];
$active_sessions = [];

try {
    $logs = $pdo->prepare("
        SELECT *
        FROM login_logs
        WHERE admin_id = ?
        ORDER BY login_time DESC
        LIMIT 10
    ");

    $logs->execute([$admin_id]);
    $activities = $logs->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Les tables d'audit peuvent etre absentes selon le schema.
    $activities = [];
}

try {
    $sessions = $pdo->prepare("
        SELECT *
        FROM active_sessions
        WHERE admin_id = ?
        ORDER BY last_activity DESC
    ");

    $sessions->execute([$admin_id]);
    $active_sessions = $sessions->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Fallback silencieux si la table n'existe pas.
    $active_sessions = [];
}



/* =========================================================
   STATS
========================================================= */

$total_students = $pdo->query("
    SELECT COUNT(*) FROM etudiants
")->fetchColumn();

$total_teachers = $pdo->query("
    SELECT COUNT(*) FROM enseignants
")->fetchColumn();

$total_subjects = $pdo->query("
    SELECT COUNT(*) FROM matieres
")->fetchColumn();

$total_notes = $pdo->query("
    SELECT COUNT(*) FROM notes
")->fetchColumn();

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Profile Administrateur</title>

    <link rel="stylesheet"
          href="../assets/css/settings.css">

    <script src="https://unpkg.com/lucide@latest"></script>

</head>

<body>

<!-- =========================================================
     BACKGROUND EFFECTS
========================================================= -->

<div class="floating-elements">

    <div class="floating circle-one"></div>
    <div class="floating circle-two"></div>
    <div class="floating square-one"></div>
    <div class="floating square-two"></div>
    <div class="floating line-one"></div>

</div>

<div class="app-container">

<!-- =========================================================
     SIDEBAR
========================================================= -->

<aside class="sidebar">

    <div>

        <div class="brand">

            <div class="logo-box">
                🎓
            </div>

            <div>

                <h2 class="logo">
                    EduManage
                </h2>

                <p class="logo-subtitle">
                    Administration Panel
                </p>

            </div>

        </div>

        <ul class="menu">

            <li class="menu-item">
                <a href="dashboard.php">
                    <i data-lucide="layout-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="menu-item">
                <a href="students.php">
                    <i data-lucide="graduation-cap"></i>
                    <span>Étudiants</span>
                </a>
            </li>

            <li class="menu-item">
                <a href="teachers.php">
                    <i data-lucide="users"></i>
                    <span>Enseignants</span>
                </a>
            </li>

            <li class="menu-item">
                <a href="subjects.php">
                    <i data-lucide="book-open"></i>
                    <span>Matières</span>
                </a>
            </li>

            <li class="menu-item">
                <a href="schedule.php">
                    <i data-lucide="calendar-days"></i>
                    <span>Emploi du Temps</span>
                </a>
            </li>

            <li class="menu-item">
                <a href="notes.php">
                    <i data-lucide="clipboard-list"></i>
                    <span>Notes</span>
                </a>
            </li>

            <li class="menu-item active">
                <a href="settings.php">
                    <i data-lucide="settings"></i>
                    <span>Profile</span>
                </a>
            </li>

        </ul>

    </div>

   <!-- FOOTER -->

    <div class="sidebar-footer">

        <div class="admin-profile">

            <div class="avatar">
                <?= strtoupper(substr($_SESSION['user_nom'] ?? 'A', 0, 1)); ?>
            </div>

            <div>

                <h4>
                    <?php echo htmlspecialchars($_SESSION['user_nom'] ?? 'Admin'); ?>
                </h4>

                <span>
                    Administrateur
                </span>

            </div>

        </div>

        <a href="../api/logout.php"
           class="logout-btn">

            <i data-lucide="log-out"></i>

            Déconnexion

        </a>

    </div>

</aside>

<!-- =========================================================
     MAIN CONTENT
========================================================= -->

<main class="main-content">

    <!-- ALERTS -->

    <?php if(!empty($message)): ?>

        <div class="success-message">

            <i data-lucide="badge-check"></i>

            <?= htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>

    <?php if(!empty($error)): ?>

        <div class="error-message">

            <i data-lucide="alert-circle"></i>

            <?= htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>

    <!-- HEADER -->

    <div class="page-header">

        <div class="header-info">

            <span class="badge-accent">
                Configuration avancée
            </span>

            <h1>
                Profile Administrateur
            </h1>

            <p>
                Gérez votre profil, votre sécurité et les statistiques globales.
            </p>

        </div>

    </div>

    <!-- STATS -->

    <div class="stats-grid">

        <div class="stat-card glass-panel">

            <div class="stat-icon blue">
                <i data-lucide="graduation-cap"></i>
            </div>

            <div class="stat-details">

                <h3>Étudiants</h3>

                <p class="stat-number">

                    <?= $total_students; ?>

                </p>

            </div>

        </div>

        <div class="stat-card glass-panel">

            <div class="stat-icon purple">
                <i data-lucide="users"></i>
            </div>

            <div class="stat-details">

                <h3>Enseignants</h3>

                <p class="stat-number">

                    <?= $total_teachers; ?>

                </p>

            </div>

        </div>

        <div class="stat-card glass-panel">

            <div class="stat-icon orange">
                <i data-lucide="book-open"></i>
            </div>

            <div class="stat-details">

                <h3>Matières</h3>

                <p class="stat-number">

                    <?= $total_subjects; ?>

                </p>

            </div>

        </div>

        <div class="stat-card glass-panel">

            <div class="stat-icon green">
                <i data-lucide="clipboard-check"></i>
            </div>

            <div class="stat-details">

                <h3>Notes</h3>

                <p class="stat-number">

                    <?= $total_notes; ?>

                </p>

            </div>

        </div>

    </div>

    <!-- SETTINGS GRID -->

    <div class="settings-grid">

        <!-- PROFILE -->

        <div class="glass-panel settings-card">

            <div class="card-header">

                <div>

                    <h2>
                        Informations du Profil
                    </h2>

                    <p>
                        Modifiez vos informations personnelles
                    </p>

                </div>

                <div class="card-icon">
                    <i data-lucide="user-circle"></i>
                </div>

            </div>

            <form method="POST"
                  class="premium-form">

                <div class="form-row">

                    <div class="form-group">

                        <label>Nom</label>

                        <input
                            type="text"
                            name="nom"
                            class="form-control"

                            value="<?= htmlspecialchars($admin['nom'] ?? ''); ?>"

                            required
                        >

                    </div>

                    <div class="form-group">

                        <label>Prénom</label>

                        <input
                            type="text"
                            name="prenom"
                            class="form-control"

                            value="<?= htmlspecialchars($admin['prenom'] ?? ''); ?>"

                            required
                        >

                    </div>

                </div>

                <div class="form-group">

                    <label>Email</label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"

                        value="<?= htmlspecialchars($admin['email'] ?? ''); ?>"

                        required
                    >

                </div>

                <button type="submit"
                        name="update_profile"
                        class="btn-save">

                    <i data-lucide="save"></i>

                    Sauvegarder les modifications

                </button>

            </form>

        </div>

        <!-- SECURITY -->

        <div class="glass-panel settings-card">

            <div class="card-header">

                <div>

                    <h2>
                        Sécurité du Compte
                    </h2>

                    <p>
                        Modifiez votre mot de passe administrateur
                    </p>

                </div>

                <div class="card-icon purple-bg">
                    <i data-lucide="shield-check"></i>
                </div>

            </div>

            <form method="POST"
                  class="premium-form">

                <div class="form-group">

                    <label>Mot de passe actuel</label>

                    <input
                        type="password"
                        name="current_password"
                        class="form-control"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Nouveau mot de passe</label>

                    <input
                        type="password"
                        name="new_password"
                        class="form-control"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Confirmer le mot de passe</label>

                    <input
                        type="password"
                        name="confirm_password"
                        class="form-control"
                        required
                    >

                </div>

                <button type="submit"
                        name="update_password"
                        class="btn-save purple-btn">

                    <i data-lucide="lock"></i>

                    Modifier le mot de passe

                </button>

            </form>

        </div>

    </div>

</main>

</div>

<script>

    lucide.createIcons();

</script>

</body>
</html>