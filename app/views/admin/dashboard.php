<?php
/**
 * Variables disponibles:
 * $stats - Array contenant les statistiques (total_students, total_teachers, etc)
 */
?>
<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Dashboard Admin - EduManage
    </title>

    <!-- CSS -->
    <link rel="stylesheet"
          href="<?php echo assetUrl('css/dashboardadmin.css'); ?>">

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

    <!-- SIDEBAR -->
    <?php include basePath('app/views/layouts/admin_sidebar.php'); ?>

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
                        <?php echo htmlspecialchars($_SESSION['user_nom']); ?>

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
                        📊
                    </div>

                </div>

            </div>

        </section>

        <!-- STATISTICS SECTION -->

        <section class="statistics">

            <div class="stat-card">

                <div class="stat-icon students">
                    <i data-lucide="graduation-cap"></i>
                </div>

                <div class="stat-content">

                    <h3>
                        Étudiants
                    </h3>

                    <p class="stat-number">
                        <?php echo $stats['total_students'] ?? 0; ?>
                    </p>

                    <span class="stat-label">
                        Total enregistrés
                    </span>

                </div>

            </div>

            <div class="stat-card">

                <div class="stat-icon teachers">
                    <i data-lucide="users"></i>
                </div>

                <div class="stat-content">

                    <h3>
                        Enseignants
                    </h3>

                    <p class="stat-number">
                        <?php echo $stats['total_teachers'] ?? 0; ?>
                    </p>

                    <span class="stat-label">
                        Total enregistrés
                    </span>

                </div>

            </div>

            <div class="stat-card">

                <div class="stat-icon subjects">
                    <i data-lucide="book-open"></i>
                </div>

                <div class="stat-content">

                    <h3>
                        Matières
                    </h3>

                    <p class="stat-number">
                        <?php echo $stats['total_subjects'] ?? 0; ?>
                    </p>

                    <span class="stat-label">
                        Total actives
                    </span>

                </div>

            </div>

            <div class="stat-card">

                <div class="stat-icon schedules">
                    <i data-lucide="calendar-days"></i>
                </div>

                <div class="stat-content">

                    <h3>
                        Emplois du Temps
                    </h3>

                    <p class="stat-number">
                        <?php echo $stats['total_schedules'] ?? 0; ?>
                    </p>

                    <span class="stat-label">
                        Total configurés
                    </span>

                </div>

            </div>

        </section>

        <!-- QUICK ACTIONS -->

        <section class="quick-actions">

            <h2>
                Actions Rapides
            </h2>

            <div class="actions-grid">

                <a href="<?php echo baseUrl('admin/students'); ?>"
                   class="action-btn">

                    <i data-lucide="plus-circle"></i>

                    <span>
                        Ajouter Étudiant
                    </span>

                </a>

                <a href="<?php echo baseUrl('admin/teachers'); ?>"
                   class="action-btn">

                    <i data-lucide="plus-circle"></i>

                    <span>
                        Ajouter Enseignant
                    </span>

                </a>

                <a href="<?php echo baseUrl('admin/subjects'); ?>"
                   class="action-btn">

                    <i data-lucide="plus-circle"></i>

                    <span>
                        Ajouter Matière
                    </span>

                </a>

                <a href="<?php echo baseUrl('admin/schedule'); ?>"
                   class="action-btn">

                    <i data-lucide="plus-circle"></i>

                    <span>
                        Ajouter Horaire
                    </span>

                </a>

            </div>

        </section>

    </main>

</div>

<!-- ICONS -->
<script>

    lucide.createIcons();

</script>

</body>
</html>
