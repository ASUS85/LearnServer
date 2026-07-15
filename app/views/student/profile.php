<?php
$studentName = trim(($student['prenom'] ?? '') . ' ' . ($student['nom'] ?? ''));
if ($studentName === '') {
    $studentName = 'Étudiant';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - LearnServer</title>
    <link rel="stylesheet" href="<?php echo assetUrl('css/dashboardadmin.css'); ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
<div class="container">
    <?php include basePath('app/views/layouts/student_sidebar.php'); ?>
    <main class="main-content">
        <section class="hero">
            <div class="hero-content">
                <div class="hero-text">
                    <span class="hero-badge">Mon profil</span>
                    <h1><?php echo htmlspecialchars($studentName); ?></h1>
                    <p>Informations de votre compte étudiant.</p>
                </div>
            </div>
        </section>

        <section class="stats-grid">
            <div class="stat-card"><h3>Matricule</h3><p><?php echo htmlspecialchars($student['matricule'] ?? '-'); ?></p></div>
            <div class="stat-card"><h3>Email</h3><p style="font-size:1em;"><?php echo htmlspecialchars($student['email'] ?? '-'); ?></p></div>
            <div class="stat-card"><h3>Téléphone</h3><p style="font-size:1em;"><?php echo htmlspecialchars($student['telephone'] ?? '-'); ?></p></div>
        </section>
    </main>
</div>
<script>lucide.createIcons();</script>
</body>
</html>
