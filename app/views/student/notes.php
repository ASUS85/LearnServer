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
    <title>Mes Notes - LearnServer</title>
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
                    <span class="hero-badge">Mes notes</span>
                    <h1><?php echo htmlspecialchars($studentName); ?></h1>
                    <p>Consultez l'historique de vos évaluations.</p>
                </div>
            </div>
        </section>

        <section class="glass-panel" style="padding:24px;">
            <?php if (!empty($notes)): ?>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Matière</th>
                                <th>Note</th>
                                <th>Session</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notes as $note): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($note['nom_matiere'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($note['note'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($note['session'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($note['date_creation'] ?? $note['date_note'] ?? '-'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>Aucune note disponible.</p>
            <?php endif; ?>
        </section>
    </main>
</div>
<script>lucide.createIcons();</script>
</body>
</html>
