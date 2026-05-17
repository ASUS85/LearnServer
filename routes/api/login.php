<?php

session_start();

require_once '../config/database.php';

/* =========================================================
   INITIALISATION
========================================================= */

$error = "";

$email = isset($_POST['email'])
    ? trim($_POST['email'])
    : '';

$password = isset($_POST['password'])
    ? trim($_POST['password'])
    : '';

$role = isset($_POST['role'])
    ? trim($_POST['role'])
    : '';

/* =========================================================
   VERIFICATION CHAMPS
========================================================= */

if(
    empty($email) ||
    empty($password) ||
    empty($role)
){

    $error = "Tous les champs sont obligatoires.";

}

/* =========================================================
   TABLES & REDIRECTIONS
========================================================= */

$tableMap = array(

    'admin'  => 'admins',
    'student' => 'etudiants',
    'teacher' => 'enseignants'

);

$redirectMap = array(

    'admin'  => '../admin/dashboard.php',
    'student' => '../student/dashboard.php',
    'teacher' => '../teacher/dashboard.php'

);

/* =========================================================
   VERIFICATION ROLE
========================================================= */

if(
    empty($error) &&
    !array_key_exists($role, $tableMap)
){

    $error = "Type de compte invalide.";

}

/* =========================================================
   AUTHENTIFICATION
========================================================= */

if(empty($error)){

    $table = $tableMap[$role];

    $stmt = $pdo->prepare("

        SELECT *
        FROM $table
        WHERE email = ?

    ");

    $stmt->execute([$email]);

    $user = $stmt->fetch();

    /* =====================================================
       UTILISATEUR INTROUVABLE
    ===================================================== */

    if(!$user){

        $error = "Compte introuvable.";

    }else{

        /* =================================================
           MOT DE PASSE
        ================================================= */

        if($password != $user['mot_de_passe']){

            $error = "Mot de passe incorrect.";

        }

    }

}

/* =========================================================
   SI ERREUR
========================================================= */

if(!empty($error)){

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Connexion refusée
    </title>
</head>

<body>

    <div class="error-container">

        <div class="icon">
            ✕
        </div>

        <h1>
            Accès refusé
        </h1>

        <p>

            Impossible de se connecter à la plateforme
            académique EduManage X.

        </p>

        <div class="error-message">

            <?= htmlspecialchars($error); ?>

        </div>

        <a href="../index.php"
           class="back-btn">

            Retour à la connexion

        </a>

    </div>

</body>
</html>

<?php

    exit;

}

/* =========================================================
   SESSION UTILISATEUR
========================================================= */

$_SESSION['user_id'] = $user['id'];

$_SESSION['user_nom'] = $user['nom'];

$_SESSION['user_role'] = $role;

/* =========================================================
   REDIRECTION
========================================================= */

header("Location: " . $redirectMap[$role]);

exit;

?>