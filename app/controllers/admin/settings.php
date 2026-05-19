<?php
session_start();
require_once("../config/database.php");

/* =========================================================
   SECURITE ADMIN
========================================================= */

if(
    !isset($_SESSION['user_role']) ||
    $_SESSION['user_role'] != 'admin'
){
    header("Location: ../index.php");
    exit;
}

/* =========================================================
   CREATION TABLE SETTINGS
========================================================= */

$pdo->exec("

CREATE TABLE IF NOT EXISTS settings(

    id INT PRIMARY KEY AUTO_INCREMENT,

    school_name VARCHAR(255),
    school_email VARCHAR(255),
    school_phone VARCHAR(100),
    school_address TEXT,
    school_logo VARCHAR(255),

    academic_year VARCHAR(100),
    current_semester VARCHAR(50),

    theme VARCHAR(50),
    primary_color VARCHAR(50),

    show_logo TINYINT(1) DEFAULT 1,
    show_signature TINYINT(1) DEFAULT 1,
    show_stamp TINYINT(1) DEFAULT 1,

    smtp_host VARCHAR(255),
    smtp_port VARCHAR(50),
    smtp_email VARCHAR(255),
    smtp_password VARCHAR(255),

    session_timeout INT DEFAULT 30,
    max_login_attempts INT DEFAULT 5,

    notifications_email TINYINT(1) DEFAULT 1,
    notifications_system TINYINT(1) DEFAULT 1,

    updated_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP

)

");

/* =========================================================
   INSERT DEFAULT SETTINGS
========================================================= */

$check = $pdo->query("
    SELECT *
    FROM settings
    LIMIT 1
");

if($check->rowCount() == 0){

    $pdo->exec("

    INSERT INTO settings(

        school_name,
        school_email,
        school_phone,
        school_address,
        academic_year,
        current_semester,
        theme,
        primary_color

    )

    VALUES(

        'EduManage University',
        'contact@edumanage.com',
        '+237 600000000',
        'Douala Cameroun',
        '2025 - 2026',
        'Semestre 1',
        'dark',
        'blue'

    )

    ");

}

/* =========================================================
   RECUPERATION SETTINGS
========================================================= */

$stmt = $pdo->query("
    SELECT *
    FROM settings
    LIMIT 1
");

$settings = $stmt->fetch(PDO::FETCH_ASSOC);

$message = "";
$error = "";

/* =========================================================
   SAVE SETTINGS
========================================================= */

if(isset($_POST['save_settings'])){

    $school_name = trim($_POST['school_name']);
    $school_email = trim($_POST['school_email']);
    $school_phone = trim($_POST['school_phone']);
    $school_address = trim($_POST['school_address']);

    $academic_year = trim($_POST['academic_year']);
    $current_semester = trim($_POST['current_semester']);

    $theme = trim($_POST['theme']);
    $primary_color = trim($_POST['primary_color']);

    $smtp_host = trim($_POST['smtp_host']);
    $smtp_port = trim($_POST['smtp_port']);
    $smtp_email = trim($_POST['smtp_email']);
    $smtp_password = trim($_POST['smtp_password']);

    $session_timeout = intval($_POST['session_timeout']);
    $max_login_attempts = intval($_POST['max_login_attempts']);

    $show_logo = isset($_POST['show_logo']) ? 1 : 0;
    $show_signature = isset($_POST['show_signature']) ? 1 : 0;
    $show_stamp = isset($_POST['show_stamp']) ? 1 : 0;

    $notifications_email =
        isset($_POST['notifications_email']) ? 1 : 0;

    $notifications_system =
        isset($_POST['notifications_system']) ? 1 : 0;

    /* =====================================================
       LOGO UPLOAD
    ===================================================== */

    $logo_name = $settings['school_logo'];

    if(
        isset($_FILES['school_logo']) &&
        $_FILES['school_logo']['error'] == 0
    ){

        $upload_dir = "../uploads/";

        if(!is_dir($upload_dir)){
            mkdir($upload_dir);
        }

        $file_name =
            time() . "_" .
            basename($_FILES['school_logo']['name']);

        $target =
            $upload_dir . $file_name;

        move_uploaded_file(
            $_FILES['school_logo']['tmp_name'],
            $target
        );

        $logo_name = $file_name;

    }

    /* =====================================================
       UPDATE
    ===================================================== */

    $sql = "

    UPDATE settings SET

        school_name = ?,
        school_email = ?,
        school_phone = ?,
        school_address = ?,
        school_logo = ?,

        academic_year = ?,
        current_semester = ?,

        theme = ?,
        primary_color = ?,

        show_logo = ?,
        show_signature = ?,
        show_stamp = ?,

        smtp_host = ?,
        smtp_port = ?,
        smtp_email = ?,
        smtp_password = ?,

        session_timeout = ?,
        max_login_attempts = ?,

        notifications_email = ?,
        notifications_system = ?

    WHERE id = ?

    ";

    $update = $pdo->prepare($sql);

    $update->execute([

        $school_name,
        $school_email,
        $school_phone,
        $school_address,
        $logo_name,

        $academic_year,
        $current_semester,

        $theme,
        $primary_color,

        $show_logo,
        $show_signature,
        $show_stamp,

        $smtp_host,
        $smtp_port,
        $smtp_email,
        $smtp_password,

        $session_timeout,
        $max_login_attempts,

        $notifications_email,
        $notifications_system,

        $settings['id']

    ]);

    $message = "Paramètres enregistrés avec succès.";

    $stmt = $pdo->query("
        SELECT *
        FROM settings
        LIMIT 1
    ");

    $settings = $stmt->fetch(PDO::FETCH_ASSOC);

}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Paramètres Avancés</title>

<link rel="stylesheet"
      href="../../assets/css/settings.css">

</head>

<body>

<div class="container">

<!-- =====================================================
     MAIN CONTENT
===================================================== -->

<main class="main-content">

    <div class="topbar">

        <div>

            <h1>
                Paramètres Avancés
            </h1>

            <p>
                Configuration complète du système académique
            </p>

        </div>

    </div>

    <?php if(!empty($message)): ?>

        <div class="success-message">

            <?= $message; ?>

        </div>

    <?php endif; ?>

    <?php if(!empty($error)): ?>

        <div class="error-message">

            <?= $error; ?>

        </div>

    <?php endif; ?>

<form method="POST"
      enctype="multipart/form-data">

<!-- =====================================================
     ETABLISSEMENT
===================================================== -->

<div class="settings-card">

    <h2>
        🏫 Informations Établissement
    </h2>

    <div class="form-grid">

        <div class="form-group">

            <label>
                Nom Établissement
            </label>

            <input type="text"
                   name="school_name"

                   value="<?= htmlspecialchars($settings['school_name']); ?>">

        </div>

        <div class="form-group">

            <label>
                Email
            </label>

            <input type="email"
                   name="school_email"

                   value="<?= htmlspecialchars($settings['school_email']); ?>">

        </div>

        <div class="form-group">

            <label>
                Téléphone
            </label>

            <input type="text"
                   name="school_phone"

                   value="<?= htmlspecialchars($settings['school_phone']); ?>">

        </div>

        <div class="form-group">

            <label>
                Année Académique
            </label>

            <input type="text"
                   name="academic_year"

                   value="<?= htmlspecialchars($settings['academic_year']); ?>">

        </div>

    </div>

    <div class="form-group">

        <label>
            Adresse
        </label>

        <textarea name="school_address"><?= htmlspecialchars($settings['school_address']); ?></textarea>

    </div>

</div>

<!-- =====================================================
     LOGO
===================================================== -->

<div class="settings-card">

    <h2>
        🖼 Logo Établissement
    </h2>

    <?php if(!empty($settings['school_logo'])): ?>

        <img
            src="../uploads/<?= $settings['school_logo']; ?>"
            class="logo-preview"
        >

    <?php endif; ?>

    <div class="form-group">

        <label>
            Choisir un logo
        </label>

        <input type="file"
               name="school_logo">

    </div>

</div>

<!-- =====================================================
     APPARENCE
===================================================== -->

<div class="settings-card">

    <h2>
        🎨 Apparence
    </h2>

    <div class="form-grid">

        <div class="form-group">

            <label>
                Thème
            </label>

            <select name="theme">

                <option value="dark">
                    Dark
                </option>

                <option value="light">
                    Light
                </option>

            </select>

        </div>

        <div class="form-group">

            <label>
                Couleur Principale
            </label>

            <select name="primary_color">

                <option value="blue">
                    Bleu
                </option>

                <option value="green">
                    Vert
                </option>

                <option value="purple">
                    Violet
                </option>

            </select>

        </div>

    </div>

</div>

<!-- =====================================================
     IMPRESSION
===================================================== -->

<div class="settings-card">

    <h2>
        🖨 Paramètres Impression
    </h2>

    <div class="checkbox-group">

        <label>

            <input type="checkbox"
                   name="show_logo"

                   <?= $settings['show_logo'] ? 'checked' : ''; ?>>

            Afficher Logo

        </label>

        <label>

            <input type="checkbox"
                   name="show_signature"

                   <?= $settings['show_signature'] ? 'checked' : ''; ?>>

            Afficher Signature

        </label>

        <label>

            <input type="checkbox"
                   name="show_stamp"

                   <?= $settings['show_stamp'] ? 'checked' : ''; ?>>

            Afficher Cachet

        </label>

    </div>

</div>

<!-- =====================================================
     SMTP
===================================================== -->

<div class="settings-card">

    <h2>
        📧 SMTP / Email
    </h2>

    <div class="form-grid">

        <div class="form-group">

            <label>
                SMTP Host
            </label>

            <input type="text"
                   name="smtp_host"

                   value="<?= htmlspecialchars($settings['smtp_host']); ?>">

        </div>

        <div class="form-group">

            <label>
                SMTP Port
            </label>

            <input type="text"
                   name="smtp_port"

                   value="<?= htmlspecialchars($settings['smtp_port']); ?>">

        </div>

        <div class="form-group">

            <label>
                SMTP Email
            </label>

            <input type="email"
                   name="smtp_email"

                   value="<?= htmlspecialchars($settings['smtp_email']); ?>">

        </div>

        <div class="form-group">

            <label>
                SMTP Password
            </label>

            <input type="password"
                   name="smtp_password"

                   value="<?= htmlspecialchars($settings['smtp_password']); ?>">

        </div>

    </div>

</div>

<!-- =====================================================
     SECURITE
===================================================== -->

<div class="settings-card">

    <h2>
        🔒 Sécurité
    </h2>

    <div class="form-grid">

        <div class="form-group">

            <label>
                Timeout Session
            </label>

            <input type="number"
                   name="session_timeout"

                   value="<?= htmlspecialchars($settings['session_timeout']); ?>">

        </div>

        <div class="form-group">

            <label>
                Tentatives Connexion
            </label>

            <input type="number"
                   name="max_login_attempts"

                   value="<?= htmlspecialchars($settings['max_login_attempts']); ?>">

        </div>

    </div>

</div>

<!-- =====================================================
     NOTIFICATIONS
===================================================== -->

<div class="settings-card">

    <h2>
        🔔 Notifications
    </h2>

    <div class="checkbox-group">

        <label>

            <input type="checkbox"
                   name="notifications_email"

                   <?= $settings['notifications_email'] ? 'checked' : ''; ?>>

            Notifications Email

        </label>

        <label>

            <input type="checkbox"
                   name="notifications_system"

                   <?= $settings['notifications_system'] ? 'checked' : ''; ?>>

            Notifications Système

        </label>

    </div>

</div>

<!-- =====================================================
     SAVE BUTTON
===================================================== -->

<button type="submit"
        name="save_settings"
        class="save-btn">

    💾 Enregistrer Tous les Paramètres

</button>

</form>

</main>

</div>

</body>
</html>