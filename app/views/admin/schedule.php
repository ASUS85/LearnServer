<?php
/**
 * Vue Emploi du Temps
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emploi du Temps - EduManage</title>
    <link rel="stylesheet" href="<?php echo assetUrl('css/schedule.css'); ?>">
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

<div class="app-container">
    <?php include basePath('app/views/layouts/admin_sidebar.php'); ?>

    <main class="main-content">
        <section class="section-header">
            <div class="header-content">
                <h1><i data-lucide="calendar-days"></i> Emploi du Temps</h1>
                <p>Gérez les horaires des cours</p>
            </div>
        </section>

        <section class="section-content">
            <div class="info-box">
                <p>Module de gestion de l'emploi du temps - À implémenter selon vos besoins spécifiques</p>
            </div>

            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Matière</th>
                            <th>Enseignant</th>
                            <th>Classe</th>
                            <th>Jour</th>
                            <th>Heure Début</th>
                            <th>Heure Fin</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($schedules)): ?>
                            <?php foreach($schedules as $schedule): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($schedule['nom_matiere'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars(($schedule['nom'] ?? '') . ' ' . ($schedule['prenom'] ?? '')); ?></td>
                                    <td><?php echo htmlspecialchars($schedule['nom_classe'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($schedule['jour'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($schedule['heure_debut'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($schedule['heure_fin'] ?? '-'); ?></td>
                                    <td class="actions">
                                        <button class="btn-small btn-edit"><i data-lucide="edit"></i></button>
                                        <button class="btn-small btn-delete"><i data-lucide="trash"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" style="text-align: center; padding: 20px;">Aucun horaire configuré</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<script>
    lucide.createIcons();
</script>
</body>
</html>
