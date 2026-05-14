<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Gestion des Cours</title>
    <link rel="stylesheet" href="assets/css/login.css">
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

<div class="login-container">
    
    <div class="login-left">
        <h1>EduManage Pro</h1>
        <p>Plateforme intelligente de gestion académique</p>
    </div>

    <div class="login-right">
        <form method="POST" action="api/login.php">

            <h2>Connexion</h2>

            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="input-group">
                <label>Mot de passe</label>
                <input type="password" name="password" required>
            </div>

            <div class="input-group">
                <label>Type de compte</label>
                <select name="role" required>
                    <option value="">Choisir...</option>
                    <option value="admin">Administrateur</option>
                    <option value="student">Étudiant</option>
                    <option value="teacher">Enseignant</option>
                </select>
            </div>

            <button type="submit">Se connecter</button>

            <p id="message"></p>

        </form>
    </div>

</div>

<script src="assets/js/login.js"></script>
</body>
</html>