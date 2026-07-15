<?php
$teacherName = trim(($teacher['prenom'] ?? '') . ' ' . ($teacher['nom'] ?? ''));
if ($teacherName === '') {
    $teacherName = 'Enseignant';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Enseignant - LearnServer</title>
    <link rel="stylesheet" href="<?php echo assetUrl('css/dashboardadmin.css'); ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

<div class="floating-elements">
    <div class="floating book"></div>
    <div class="floating graduation"></div>
    <div class="floating pencil"></div>
    <div class="floating atom"></div>
    <div class="floating globe"></div>
</div>

<div class="container">
    <?php include basePath('app/views/layouts/teacher_sidebar.php'); ?>

    <main class="main-content">
        <section class="hero">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <div class="hero-text">
                    <span class="hero-badge">Profil enseignant</span>
                    <h1><?php echo htmlspecialchars($teacherName); ?></h1>
                    <p>Vos informations de compte et vos coordonnées actuelles.</p>
                </div>
                <div class="hero-illustration">
                    <div class="circle circle1"></div>
                    <div class="circle circle2"></div>
                    <div class="circle circle3"></div>
                    <div class="main-icon">👨‍🏫</div>
                </div>
            </div>
        </section>

        <section class="stats-grid">
            <div class="stat-card">
                <div class="card-glow"></div>
                <div class="stat-icon blue"><i data-lucide="badge-check"></i></div>
                <h3>Matricule</h3>
                <p style="font-size:1em;"><?php echo htmlspecialchars($teacher['matricule'] ?? '-'); ?></p>
            </div>
            <div class="stat-card">
                <div class="card-glow"></div>
                <div class="stat-icon purple"><i data-lucide="mail"></i></div>
                <h3>Email</h3>
                <p style="font-size:1em;"><?php echo htmlspecialchars($teacher['email'] ?? '-'); ?></p>
            </div>
            <div class="stat-card">
                <div class="card-glow"></div>
                <div class="stat-icon orange"><i data-lucide="phone"></i></div>
                <h3>Téléphone</h3>
                <p style="font-size:1em;"><?php echo htmlspecialchars($teacher['telephone'] ?? '-'); ?></p>
            </div>
        </section>
    </main>
</div>

<script>
    lucide.createIcons();
</script>
</body>
</html>