<?php

session_start();

require_once("config/database.php");

$message = "";
$error = "";

/* =========================================================
   REGISTER USER
========================================================= */

if(isset($_POST['register'])){

    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = trim($_POST['role']);

    // VERIFICATION

    if(
        !empty($nom) &&
        !empty($prenom) &&
        !empty($email) &&
        !empty($password) &&
        !empty($role)
    ){

        // PASSWORD CHECK

        if($password !== $confirm_password){

            $error = "Les mots de passe ne correspondent pas.";

        }else{

            // HASH PASSWORD

            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // CHECK EMAIL

            if($role == "student"){

                $check = $pdo->prepare("
                    SELECT id
                    FROM etudiants
                    WHERE email = ?
                ");

                $check->execute([$email]);

            }

            elseif($role == "teacher"){

                $check = $pdo->prepare("
                    SELECT id
                    FROM enseignants
                    WHERE email = ?
                ");

                $check->execute([$email]);

            }

            else{

                $check = $pdo->prepare("
                    SELECT id
                    FROM admins
                    WHERE email = ?
                ");

                $check->execute([$email]);

            }

            if($check->rowCount() > 0){

                $error = "Cet email existe déjà.";

            }else{

                // GENERATE MATRICULE

                $matricule = strtoupper(substr($role,0,3))
                            . rand(1000,9999);

                // INSERT USER

                if($role == "student"){

                    $sql = "
                        INSERT INTO etudiants(
                            matricule,
                            nom,
                            prenom,
                            email,
                            mot_de_passe,
                            telephone
                        )
                        VALUES(?,?,?,?,?,?)
                    ";

                }

                elseif($role == "teacher"){

                    $sql = "
                        INSERT INTO enseignants(
                            matricule,
                            nom,
                            prenom,
                            email,
                            mot_de_passe,
                            telephone
                        )
                        VALUES(?,?,?,?,?,?)
                    ";

                }

                else{

                    $sql = "
                        INSERT INTO admins(
                            nom,
                            prenom,
                            email,
                            mot_de_passe
                        )
                        VALUES(?,?,?,?)
                    ";

                }

                $stmt = $pdo->prepare($sql);

                // EXECUTION

                if($role == "admin"){

                    $stmt->execute([
                        $nom,
                        $prenom,
                        $email,
                        $hashed_password
                    ]);

                }else{

                    $stmt->execute([
                        $matricule,
                        $nom,
                        $prenom,
                        $email,
                        $hashed_password,
                        $telephone
                    ]);

                }

                $message = "Compte créé avec succès.";

            }

        }

    }else{

        $error = "Veuillez remplir tous les champs.";

    }

}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        EduManage Pro - Inscription
    </title>

    <!-- CSS -->

    <link rel="stylesheet"
          href="../../assets/css/register.css">

    <!-- ICONS -->

    <script src="https://unpkg.com/lucide@latest"></script>

</head>

<body>

<!-- =========================================================
     FLOATING BACKGROUND
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
     CONTAINER
========================================================= -->

<div class="login-container">

    <!-- LEFT -->

    <div class="login-left">

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

        <div class="hero-content">

            <div class="hero-badge">

                <i data-lucide="sparkles"></i>

                Smart Registration System

            </div>

            <h2>

                Rejoignez la plateforme
                <span>
                    académique intelligente
                </span>

            </h2>

            <p>

                Créez votre compte sécurisé et accédez
                à un environnement académique moderne
                et professionnel.

            </p>

        </div>

    </div>

    <!-- RIGHT -->

    <div class="login-right">

        <div class="login-card">

            <!-- HEADER -->

            <div class="login-header">

                <h3>
                    Inscription
                </h3>

                <p>
                    Créez votre compte académique
                </p>

            </div>

            <!-- ALERTS -->

            <?php if(!empty($message)): ?>

                <div class="success-message">
                    <?= htmlspecialchars($message); ?>
                </div>

            <?php endif; ?>

            <?php if(!empty($error)): ?>

                <div class="error-message">
                    <?= htmlspecialchars($error); ?>
                </div>

            <?php endif; ?>

            <!-- FORM -->

            <form method="POST"
                  class="login-form">

                <!-- NOM -->

                <div class="input-group">

                    <label>
                        Nom
                    </label>

                    <div class="input-wrapper">

                        <i data-lucide="user"></i>

                        <input
                            type="text"
                            name="nom"

                            placeholder="Votre nom"

                            required
                        >

                    </div>

                </div>

                <!-- PRENOM -->

                <div class="input-group">

                    <label>
                        Prénom
                    </label>

                    <div class="input-wrapper">

                        <i data-lucide="user-round"></i>

                        <input
                            type="text"
                            name="prenom"

                            placeholder="Votre prénom"

                            required
                        >

                    </div>

                </div>

                <!-- EMAIL -->

                <div class="input-group">

                    <label>
                        Email
                    </label>

                    <div class="input-wrapper">

                        <i data-lucide="mail"></i>

                        <input
                            type="email"
                            name="email"

                            placeholder="Votre email"

                            required
                        >

                    </div>

                </div>

                <!-- PHONE -->

                <div class="input-group">

                    <label>
                        Téléphone
                    </label>

                    <div class="input-wrapper">

                        <i data-lucide="phone"></i>

                        <input
                            type="text"
                            name="telephone"

                            placeholder="Votre téléphone"
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

                            placeholder="Mot de passe"

                            required
                        >

                    </div>

                </div>

                <!-- CONFIRM -->

                <div class="input-group">

                    <label>
                        Confirmer mot de passe
                    </label>

                    <div class="input-wrapper">

                        <i data-lucide="shield-check"></i>

                        <input
                            type="password"
                            name="confirm_password"

                            placeholder="Confirmez le mot de passe"

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

                        <i data-lucide="users"></i>

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

                <!-- BUTTON -->

                <button type="submit"
                        name="register"
                        class="login-btn">

                    <span>
                        Créer un compte
                    </span>

                </button>

            </form>

            <!-- FOOTER -->

            <div class="register-link">

                Vous avez déjà un compte ?

                <a href="index.php">
                    Se connecter
                </a>

            </div>

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

<script>

    lucide.createIcons();

</script>

</body>
</html>