<?php
/**
 * Vue Profile admin
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - EduManage</title>
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

<div class="app-container">
    <?php include basePath('app/views/layouts/admin_sidebar.php'); ?>

    <main class="main-content">
        <section class="section-header">
            <div class="header-content">
                <h1><i data-lucide="user-circle"></i> Profile</h1>
                <p>Gérez vos informations et votre sécurité</p>
            </div>
        </section>

        <?php if (!empty($message)): ?>
            <div class="success-message">
                <i data-lucide="badge-check"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <i data-lucide="alert-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <section class="section-content">
            <div class="settings-container">
                <div class="settings-card">
                    <h3><i data-lucide="user"></i> Informations du Profile</h3>
                    <p>Modifiez les informations du compte connecté</p>

                    <form method="POST" class="premium-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nom</label>
                                <input type="text" name="nom" class="form-control" value="<?php echo htmlspecialchars($admin['nom'] ?? ''); ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Prénom</label>
                                <input type="text" name="prenom" class="form-control" value="<?php echo htmlspecialchars($admin['prenom'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($admin['email'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Téléphone</label>
                            <input type="text" name="telephone" class="form-control" value="<?php echo htmlspecialchars($admin['telephone'] ?? ''); ?>">
                        </div>

                        <button type="submit" name="update_profile" class="btn-save">
                            <i data-lucide="save"></i>
                            Sauvegarder
                        </button>
                    </form>
                </div>

                <div class="settings-card">
                    <h3><i data-lucide="shield-check"></i> Mot de passe</h3>
                    <p>Modifiez le mot de passe du compte connecté</p>

                    <form method="POST" class="premium-form">
                        <div class="form-group">
                            <label>Mot de passe actuel</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Nouveau mot de passe</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Confirmer le mot de passe</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>

                        <button type="submit" name="update_password" class="btn-save">
                            <i data-lucide="lock"></i>
                            Mettre à jour le mot de passe
                        </button>
                    </form>
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
