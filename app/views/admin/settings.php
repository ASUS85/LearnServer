<?php
/**
 * Vue Paramètres
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - EduManage</title>
    <link rel="stylesheet" href="<?php echo assetUrl('css/settings.css'); ?>">
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
                <h1><i data-lucide="settings"></i> Paramètres</h1>
                <p>Configurez l'application</p>
            </div>
        </section>

        <section class="section-content">
            <div class="settings-container">
                <div class="settings-card">
                    <h3><i data-lucide="building"></i> Paramètres de l'établissement</h3>
                    <p>Configurez les informations générales de votre établissement</p>
                </div>

                <div class="settings-card">
                    <h3><i data-lucide="users"></i> Gestion des utilisateurs</h3>
                    <p>Gérez les permissions et rôles des utilisateurs</p>
                </div>

                <div class="settings-card">
                    <h3><i data-lucide="database"></i> Base de données</h3>
                    <p>Gérez les sauvegardes et la maintenance</p>
                </div>

                <div class="settings-card">
                    <h3><i data-lucide="shield"></i> Sécurité</h3>
                    <p>Configurez les paramètres de sécurité</p>
                </div>
            </div>
        </section>
    </main>
</div>

<script>
    lucide.createIcons();
</script>
</body>
</html>
