<?php
session_start();
require_once("../config/database.php");

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

/* =========================
   STATISTIQUES DYNAMIQUES
========================= */

$totalStudents = $pdo->query("
    SELECT COUNT(*) 
    FROM etudiants
")->fetchColumn();

$totalTeachers = $pdo->query("
    SELECT COUNT(*) 
    FROM enseignants
")->fetchColumn();

$totalSubjects = $pdo->query("
    SELECT COUNT(*) 
    FROM matieres
")->fetchColumn();

$totalSchedules = $pdo->query("
    SELECT COUNT(*) 
    FROM emplois_temps
")->fetchColumn();

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Dashboard Administrateur
    </title>

    <!-- CSS -->
    <link rel="stylesheet"
          href="../assets/css/dashboardadmin.css">

    <!-- ICONS -->
    <script src="https://unpkg.com/lucide@latest"></script>

</head>

<body>

<!-- =========================
     BACKGROUND EFFECTS
========================= -->

<div class="floating-elements">

    <div class="floating book"></div>
    <div class="floating graduation"></div>
    <div class="floating pencil"></div>
    <div class="floating atom"></div>
    <div class="floating globe"></div>

</div>

<!-- =========================
     CONTAINER
========================= -->

<div class="container">

    <!-- =========================
         SIDEBAR
    ========================== -->

    <aside class="sidebar">

        <!-- TOP -->

        <div>

            <div class="brand">

                <div class="logo-box">
                    🎓
                </div>

                <div>

                    <h2 class="logo">
                        EduManage
                    </h2>

                    <span class="logo-subtitle">
                        Academic Prestige
                    </span>

                </div>

            </div>

            <!-- MENU -->

            <ul class="menu">

                <li class="menu-item active">

                    <a href="dashboard.php">

                        <i data-lucide="layout-dashboard"></i>

                        <span>
                            Dashboard
                        </span>

                    </a>

                </li>

                <li class="menu-item">

                    <a href="students.php">

                        <i data-lucide="graduation-cap"></i>

                        <span>
                            Étudiants
                        </span>

                    </a>

                </li>

                <li class="menu-item">

                    <a href="teachers.php">

                        <i data-lucide="users"></i>

                        <span>
                            Enseignants
                        </span>

                    </a>

                </li>

                <li class="menu-item">

                    <a href="subjects.php">

                        <i data-lucide="book-open"></i>

                        <span>
                            Matières
                        </span>

                    </a>

                </li>

                <li class="menu-item">

                    <a href="schedule.php">

                        <i data-lucide="calendar-days"></i>

                        <span>
                            Emploi du Temps
                        </span>

                    </a>

                </li>

                <li class="menu-item">

                    <a href="notes.php">

                        <i data-lucide="clipboard-list"></i>

                        <span>
                            Notes
                        </span>

                    </a>

                </li>

                <li class="menu-item">

                    <a href="settings.php">

                        <i data-lucide="settings"></i>

                        <span>
                            Paramètres
                        </span>

                    </a>

                </li>

            </ul>

        </div>

        <!-- BOTTOM -->

        <div class="sidebar-footer">

            <div class="admin-profile">

                <div class="avatar">
                    A
                </div>

                <div>

                    <h4>
                        <?php echo $_SESSION['user_nom']; ?>
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

    <!-- =========================
         MAIN CONTENT
    ========================== -->

    <main class="main-content">

        <!-- HERO SECTION -->

        <section class="hero">

            <div class="hero-overlay"></div>

            <div class="hero-content">

                <div class="hero-text">

                    <span class="hero-badge">

                        ✨ Administration Académique Premium

                    </span>

                    <h1>

                        Bienvenue,
                        <?php echo $_SESSION['user_nom']; ?>

                    </h1>

                    <p>

                        Gérez votre établissement avec
                        une plateforme moderne, élégante
                        et intelligente conçue pour les
                        institutions académiques de prestige.

                    </p>

                </div>

                <div class="hero-illustration">

                    <div class="circle circle1"></div>
                    <div class="circle circle2"></div>
                    <div class="circle circle3"></div>

                    <div class="main-icon">
                        🎓
                    </div>

                </div>

            </div>

        </section>

        <!-- =========================
             STATS CARDS
        ========================== -->

        <section class="stats-grid">

            <!-- CARD -->

            <div class="stat-card">

                <div class="card-glow"></div>

                <div class="stat-top">

                    <div class="icon-box blue">

                        <i data-lucide="graduation-cap"></i>

                    </div>

                    <span class="trend positive">
                        +12%
                    </span>

                </div>

                <h3>
                    Étudiants
                </h3>

                <p>
                    <?php echo $totalStudents; ?>
                </p>

                <small>
                    Étudiants inscrits
                </small>

            </div>

            <!-- CARD -->

            <div class="stat-card">

                <div class="card-glow"></div>

                <div class="stat-top">

                    <div class="icon-box purple">

                        <i data-lucide="users"></i>

                    </div>

                    <span class="trend positive">
                        +5%
                    </span>

                </div>

                <h3>
                    Enseignants
                </h3>

                <p>
                    <?php echo $totalTeachers; ?>
                </p>

                <small>
                    Personnel académique
                </small>

            </div>

            <!-- CARD -->

            <div class="stat-card">

                <div class="card-glow"></div>

                <div class="stat-top">

                    <div class="icon-box orange">

                        <i data-lucide="book-open"></i>

                    </div>

                    <span class="trend positive">
                        +8%
                    </span>

                </div>

                <h3>
                    Matières
                </h3>

                <p>
                    <?php echo $totalSubjects; ?>
                </p>

                <small>
                    Matières disponibles
                </small>

            </div>

            <!-- CARD -->

            <div class="stat-card">

                <div class="card-glow"></div>

                <div class="stat-top">

                    <div class="icon-box green">

                        <i data-lucide="calendar-days"></i>

                    </div>

                    <span class="trend neutral">
                        Aujourd’hui
                    </span>

                </div>

                <h3>
                    Cours Planifiés
                </h3>

                <p>
                    <?php echo $totalSchedules; ?>
                </p>

                <small>
                    Séances programmées
                </small>

            </div>

        </section>

        <!-- =========================
             DASHBOARD GRID
        ========================== -->

        <section class="dashboard-grid">

            <!-- LEFT -->

            <div class="dashboard-card large">

                <div class="card-header">

                    <div>

                        <h2>
                            Activité Académique
                        </h2>

                        <p>
                            Vue générale du système
                        </p>

                    </div>

                    <button class="card-btn">

                        Voir Rapport

                    </button>

                </div>

                <div class="activity-list">

                    <div class="activity-item">

                        <div class="activity-icon blue">
                            📚
                        </div>

                        <div>

                            <h4>
                                Nouvelle matière ajoutée
                            </h4>

                            <span>
                                Système Académique
                            </span>

                        </div>

                    </div>

                    <div class="activity-item">

                        <div class="activity-icon green">
                            👨‍🎓
                        </div>

                        <div>

                            <h4>
                                Nouveaux étudiants inscrits
                            </h4>

                            <span>
                                Aujourd’hui
                            </span>

                        </div>

                    </div>

                    <div class="activity-item">

                        <div class="activity-icon orange">
                            📝
                        </div>

                        <div>

                            <h4>
                                Notes mises à jour
                            </h4>

                            <span>
                                Département pédagogique
                            </span>

                        </div>

                    </div>

                </div>

            </div>

            <!-- RIGHT -->

            <div class="dashboard-card small">

                <div class="card-header">

                    <div>

                        <h2>
                            Performance
                        </h2>

                        <p>
                            Résultats académiques
                        </p>

                    </div>

                </div>

                <div class="performance-circle">

                    <div class="circle-progress">

                        <span>
                            92%
                        </span>

                    </div>

                    <p>
                        Taux de réussite
                    </p>

                </div>

            </div>

        </section>

    </main>

</div>

<!-- =========================
     JS
========================= -->

<script>

    lucide.createIcons();

</script>

</body>
</html>