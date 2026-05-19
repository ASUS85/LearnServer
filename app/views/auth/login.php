<?php
/**
 * Afficher les erreurs de connexion
 */
$error = $error ?? '';
$message = $message ?? '';
?>
<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        EduManage Pro - Connexion
    </title>

    <!-- CSS -->
    <link rel="stylesheet"
          href="<?php echo assetUrl('css/login.css'); ?>">

    <!-- LUCIDE ICONS -->
    <script src="https://unpkg.com/lucide@latest"></script>

</head>

<body>

<!-- =========================================================
     FLOATING BACKGROUND ELEMENTS
========================================================= -->

<div class="floating-elements">

    <div class="floating book"></div>

    <div class="floating atom"></div>

    <div class="floating globe"></div>

    <div class="floating triangle"></div>

    <div class="floating formula">
        ∑
    </div>

    <div class="floating math">
        π
    </div>

</div>

<!-- =========================================================
     LOGIN CONTAINER
========================================================= -->

<div class="login-container">

    <!-- =====================================================
         LEFT PANEL
    ====================================================== -->

    <div class="login-left">

        <!-- BRAND -->

        <div class="brand">

            <div class="logo-box">
                🎓
            </div>

            <div class="brand-text">

                <h1>
                    EduManage
                </h1>

                <p>
                    Academic Intelligence Platform
                </p>

            </div>

        </div>

        <!-- HERO -->

        <div class="hero-content">

            <div class="hero-badge">

                <i data-lucide="sun"></i>

                Future Academic System

            </div>

            <h2>

                La gestion académique
                <span>
                    du futur
                </span>

            </h2>

            <p>

                Une plateforme moderne et intelligente dédiée à la gestion
                des étudiants, enseignants, matières, notes et emplois
                du temps avec une expérience utillisateur approprier.

            </p>

            <!-- FEATURES -->

            <div class="feature-list">

                <div class="feature-card">

                    <i data-lucide="graduation-cap"></i>

                    <h4>
                        Gestion Étudiants
                    </h4>

                    <p>
                        Administration moderne des profils académiques
                    </p>

                </div>

                <div class="feature-card">

                    <i data-lucide="book-open"></i>

                    <h4>
                        Matières & Notes
                    </h4>

                    <p>
                        Gestion complète des cours et évaluations
                    </p>

                </div>

                <div class="feature-card">

                    <i data-lucide="calendar-days"></i>

                    <h4>
                        Emploi du Temps
                    </h4>

                    <p>
                        Organisation intelligente des horaires
                    </p>

                </div>

                <div class="feature-card">

                    <i data-lucide="shield-check"></i>

                    <h4>
                        Sécurité Premium
                    </h4>

                    <p>
                        Protection avancée des données académiques
                    </p>

                </div>

            </div>

        </div>

    </div>

    <!-- =====================================================
         RIGHT PANEL
    ====================================================== -->

    <div class="login-right">

        <div class="login-card">

            <!-- HEADER -->

            <div class="login-header">

                <h3>
                    Connexion
                </h3>

                <p>
                    Accédez à votre espace académique sécurisé
                </p>

            </div>

            <!-- MESSAGE DE SUCCÈS -->
            <?php if (!empty($message)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- ERREUR -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- FORM -->

            <form method="POST"
                  action="<?php echo baseUrl('login'); ?>"
                  class="login-form">

                <!-- EMAIL -->

                <div class="input-group">

                    <label>
                        Adresse Email
                    </label>

                    <div class="input-wrapper">

                        <i data-lucide="mail"></i>

                        <input
                            type="email"
                            name="email"
                            placeholder="Entrez votre email"
                            required
                        >

                    </div>

                </div>

                <!-- PASSWORD -->

                <div class="input-group">

                    <label>
                        Mot de passe
                    </label>

                    <div class="input-wrapper">

                        <i data-lucide="lock"></i>

                        <input
                            type="password"
                            name="password"
                            placeholder="Entrez votre mot de passe"
                            required
                        >

                    </div>

                </div>

                <!-- ROLE -->

                <div class="input-group">

                    <label>
                        Type de compte
                    </label>

                    <div class="input-wrapper">

                        <i data-lucide="user-cog"></i>

                        <select
                            name="role"
                            required
                        >

                            <option value="">
                                Sélectionner un rôle
                            </option>

                            <option value="admin">
                                Administrateur
                            </option>

                            <option value="student">
                                Étudiant
                            </option>

                            <option value="teacher">
                                Enseignant
                            </option>

                        </select>

                    </div>

                </div>

                <!-- OPTIONS -->

                <div class="form-options">

                    <label class="checkbox-group">

                        <input type="checkbox">

                        <span>
                            Se souvenir de moi
                        </span>

                    </label>

                    <a href="#"
                       class="forgot-link">

                        Mot de passe oublié ?

                    </a>

                </div>

                <!-- BUTTON -->

                <button type="submit"
                        class="login-btn">

                    <span>
                        Se connecter
                    </span>

                </button>

                <!-- REGISTER LINK -->

                <div class="register-link">

                    Vous n'avez pas de compte ?

                    <a href="<?php echo baseUrl('register'); ?>">
                        Créer un compte
                    </a>

                </div>

            </form>

            <!-- FOOTER -->

            <div class="login-footer">

                © 2026
                <span>
                    EduManage Pro
                </span>

                — Smart Academic Platform

            </div>

        </div>

    </div>

</div>

<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script>

    lucide.createIcons();

</script>

<script src="<?php echo assetUrl('js/login.js'); ?>"></script>

</body>
</html>
