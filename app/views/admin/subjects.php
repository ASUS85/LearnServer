<?php
/**
 * Vue Gestion Matières
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Matières - EduManage</title>
    <link rel="stylesheet" href="<?php echo assetUrl('css/students.css'); ?>">
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
    <?php include basePath('app/views/layouts/admin_sidebar.php'); ?>

    <main class="main-content">
        <section class="section-header">
            <div class="header-content">
                <h1><i data-lucide="book-open"></i> Gestion des Matières</h1>
                <p>Gérez les matières et cours</p>
            </div>
        </section>

        <section class="section-content">
            <div class="info-box">
                <p>Module de gestion des matières - À implémenter selon vos besoins spécifiques</p>
            </div>

            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Nom</th>
                            <th>Classe</th>
                            <th>Crédit</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($subjects)): ?>
                            <?php foreach($subjects as $subject): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($subject['code_matiere'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($subject['nom_matiere'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($subject['nom_classe'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($subject['credit'] ?? '-'); ?></td>
                                    <td class="actions">
                                        <button class="btn-small btn-edit"><i data-lucide="edit"></i></button>
                                        <button class="btn-small btn-delete"><i data-lucide="trash"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="text-align: center; padding: 20px;">Aucune matière enregistrée</td></tr>
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
