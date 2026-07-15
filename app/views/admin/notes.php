<?php
/**
 * Vue Gestion Notes
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Notes - EduManage</title>
    <link rel="stylesheet" href="<?php echo assetUrl('css/notes.css'); ?>">
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
                <h1><i data-lucide="clipboard-list"></i> Gestion des Notes</h1>
                <p>Gérez les notes et résultats des étudiants</p>
            </div>
        </section>

        <section class="section-content">
            <div class="info-box">
                <p>Module de gestion des notes - À implémenter selon vos besoins spécifiques</p>
            </div>

            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Étudiant</th>
                            <th>Matière</th>
                            <th>Note</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($notes)): ?>
                            <?php foreach($notes as $note): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(($note['nom'] ?? '') . ' ' . ($note['prenom'] ?? '')); ?></td>
                                    <td><?php echo htmlspecialchars($note['nom_matiere'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($note['note'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($note['date_creation'] ?? '-'); ?></td>
                                    <td class="actions">
                                        <button class="btn-small btn-edit"><i data-lucide="edit"></i></button>
                                        <button class="btn-small btn-delete"><i data-lucide="trash"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="text-align: center; padding: 20px;">Aucune note enregistrée</td></tr>
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
