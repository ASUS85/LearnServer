<?php
session_start();

if (!isset($_SESSION['user_role'])) {

    header("Location: ../index.php");
    exit;

}

require_once("../config/database.php");

$message = "";
$error = "";

/* =========================================================
   AJOUT NOTE
========================================================= */

if(isset($_POST['add_note'])){

    $etudiant_id = intval($_POST['etudiant_id']);
    $matiere_id = intval($_POST['matiere_id']);

    $note = trim($_POST['note']);
    $session_note = trim($_POST['session']);
    $date_note = $_POST['date_note'];

    if(

        !empty($etudiant_id) &&
        !empty($matiere_id) &&
        $note !== "" &&
        !empty($session_note)

    ){

        $sql = "
            INSERT INTO notes(

                etudiant_id,
                matiere_id,
                note,
                session,
                date_note

            )

            VALUES(?,?,?,?,?)
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([

            $etudiant_id,
            $matiere_id,
            $note,
            $session_note,
            $date_note

        ]);

        $message = "Note ajoutée avec succès.";

    }else{

        $error = "Veuillez remplir tous les champs.";

    }

}

/* =========================================================
   SUPPRESSION
========================================================= */

if(isset($_GET['delete']) && is_numeric($_GET['delete'])){

    $id = intval($_GET['delete']);

    $sql = "
        DELETE FROM notes
        WHERE id = ?
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([$id]);

    $message = "Note supprimée avec succès.";

}

/* =========================================================
   RECUPERATION DONNEES
========================================================= */

$students = $pdo->query("
    SELECT *
    FROM etudiants
    ORDER BY nom ASC
")->fetchAll(PDO::FETCH_ASSOC);

$subjects = $pdo->query("
    SELECT *
    FROM matieres
    ORDER BY nom_matiere ASC
")->fetchAll(PDO::FETCH_ASSOC);

/* =========================================================
   RECUPERATION NOTES
========================================================= */

$notes = $pdo->query("

    SELECT

        notes.*,

        etudiants.nom,
        etudiants.prenom,
        etudiants.matricule,

        matieres.nom_matiere,
        matieres.code_matiere

    FROM notes

    INNER JOIN etudiants
    ON notes.etudiant_id = etudiants.id

    INNER JOIN matieres
    ON notes.matiere_id = matieres.id

    ORDER BY notes.date_note DESC

")->fetchAll(PDO::FETCH_ASSOC);

/* =========================================================
   STATISTIQUES
========================================================= */

$total_notes = count($notes);

$moyenne = 0;

if($total_notes > 0){

    $somme = 0;

    foreach($notes as $n){

        $somme += $n['note'];

    }

    $moyenne = $somme / $total_notes;

}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Gestion des Notes</title>

    <link rel="stylesheet"
          href="../assets/css/notes.css">

    <script src="https://unpkg.com/lucide@latest"></script>

</head>

<body>

<!-- =========================================================
     FLOATING BACKGROUND
========================================================= -->

<div class="floating-elements">

    <div class="floating orb orb1"></div>
    <div class="floating orb orb2"></div>
    <div class="floating orb orb3"></div>

</div>

<div class="app-container">

<!-- =========================================================
     SIDEBAR
========================================================= -->

<aside class="sidebar">

    <div>

        <div class="brand">

            <div class="logo-box">
                🎓
            </div>

            <div>

                <h2 class="logo">
                    EduManage
                </h2>

                <p class="logo-subtitle">
                    Academic Platform
                </p>

            </div>

        </div>

        <ul class="menu">

            <li class="menu-item">
                <a href="dashboard.php">

                    <i data-lucide="layout-dashboard"></i>

                    <span>Dashboard</span>

                </a>
            </li>

            <li class="menu-item">
                <a href="students.php">

                    <i data-lucide="graduation-cap"></i>

                    <span>Étudiants</span>

                </a>
            </li>

            <li class="menu-item">
                <a href="teachers.php">

                    <i data-lucide="users"></i>

                    <span>Enseignants</span>

                </a>
            </li>

            <li class="menu-item">
                <a href="subjects.php">

                    <i data-lucide="book-open"></i>

                    <span>Matières</span>

                </a>
            </li>

            <li class="menu-item">
                <a href="schedule.php">

                    <i data-lucide="calendar-days"></i>

                    <span>Emploi du Temps</span>

                </a>
            </li>

            <li class="menu-item active">
                <a href="notes.php">

                    <i data-lucide="clipboard-list"></i>

                    <span>Notes</span>

                </a>
            </li>

            <li class="menu-item">
                <a href="settings.php">

                    <i data-lucide="settings"></i>

                    <span>Paramètres</span>

                </a>
            </li>

        </ul>

    </div>

    <div class="sidebar-footer">

        <div class="admin-profile">

            <div class="avatar">
                <?= strtoupper(substr($_SESSION['user_nom'],0,1)); ?>
            </div>

            <div>

                <h4>
                    <?= htmlspecialchars($_SESSION['user_nom']); ?>
                </h4>

                <span>
                    Administrateur
                </span>

            </div>

        </div>

        <a href="../api/logout.php"
           class="logout-btn">

            <i data-lucide="log-out"></i>

            Déconnexion

        </a>

    </div>

</aside>

<!-- =========================================================
     MAIN CONTENT
========================================================= -->

<main class="main-content">

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

<!-- =========================================================
     PAGE HEADER
========================================================= -->

<div class="page-header">

    <div class="header-info">

        <div class="badge-accent">
            Gestion Académique
        </div>

        <h1>
            Gestion des Notes
        </h1>

        <p>
            Administration intelligente des évaluations académiques
        </p>

    </div>

    <button class="btn-primary"
            onclick="openModal()">

        <i data-lucide="plus"></i>

        Ajouter une Note

    </button>

</div>

<!-- =========================================================
     STATS
========================================================= -->

<div class="stats-grid">

    <div class="stat-card glass-panel">

        <div class="stat-icon blue">

            <i data-lucide="clipboard-check"></i>

        </div>

        <div class="stat-details">

            <h3>Total Notes</h3>

            <div class="stat-number">
                <?= $total_notes; ?>
            </div>

        </div>

    </div>

    <div class="stat-card glass-panel">

        <div class="stat-icon purple">

            <i data-lucide="bar-chart-3"></i>

        </div>

        <div class="stat-details">

            <h3>Moyenne Générale</h3>

            <div class="stat-number">
                <?= number_format($moyenne,2); ?>/20
            </div>

        </div>

    </div>

    <div class="stat-card glass-panel">

        <div class="stat-icon green">

            <i data-lucide="users"></i>

        </div>

        <div class="stat-details">

            <h3>Étudiants</h3>

            <div class="stat-number">
                <?= count($students); ?>
            </div>

        </div>

    </div>

    <div class="stat-card glass-panel">

        <div class="stat-icon orange">

            <i data-lucide="book"></i>

        </div>

        <div class="stat-details">

            <h3>Matières</h3>

            <div class="stat-number">
                <?= count($subjects); ?>
            </div>

        </div>

    </div>

</div>

<!-- =========================================================
     TABLE
========================================================= -->

<div class="glass-panel table-container">

    <table class="premium-table">

        <thead>

            <tr>

                <th>Étudiant</th>
                <th>Matière</th>
                <th>Note</th>
                <th>Session</th>
                <th>Date</th>
                <th>Statut</th>
                <th>Actions</th>

            </tr>

        </thead>

        <tbody>

        <?php if(count($notes) > 0): ?>

            <?php foreach($notes as $note): ?>

                <?php

                    $noteValue = $note['note'];

                    if($noteValue < 10){

                        $badgeClass = "danger";
                        $status = "Échec";

                    }elseif($noteValue < 14){

                        $badgeClass = "warning";
                        $status = "Passable";

                    }elseif($noteValue < 17){

                        $badgeClass = "primary";
                        $status = "Bien";

                    }else{

                        $badgeClass = "success";
                        $status = "Excellent";

                    }

                ?>

                <tr>

                    <td>

                        <div class="user-info">

                            <div class="avatar">

                                <?= strtoupper(substr($note['nom'],0,1)); ?>

                            </div>

                            <div>

                                <div class="fw-bold">

                                    <?= htmlspecialchars($note['nom']); ?>

                                    <?= htmlspecialchars($note['prenom']); ?>

                                </div>

                                <div class="text-muted">

                                    <?= htmlspecialchars($note['matricule']); ?>

                                </div>

                            </div>

                        </div>

                    </td>

                    <td>

                        <div class="fw-bold">

                            <?= htmlspecialchars($note['nom_matiere']); ?>

                        </div>

                        <div class="text-muted">

                            <?= htmlspecialchars($note['code_matiere']); ?>

                        </div>

                    </td>

                    <td>

                        <span class="note-badge <?= $badgeClass; ?>">

                            <?= htmlspecialchars($note['note']); ?>/20

                        </span>

                    </td>

                    <td>

                        <span class="badge-outline">

                            <?= htmlspecialchars($note['session']); ?>

                        </span>

                    </td>

                    <td>

                        <?= htmlspecialchars($note['date_note']); ?>

                    </td>

                    <td>

                        <span class="status-badge <?= $badgeClass; ?>">

                            <?= $status; ?>

                        </span>

                    </td>

                    <td class="actions-cell">

                        <a href="?delete=<?= $note['id']; ?>"
                           class="btn-icon delete"

                           onclick="return confirm('Supprimer cette note ?')">

                            <i data-lucide="trash-2"></i>

                        </a>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>

                <td colspan="7"
                    class="empty">

                    Aucune note enregistrée

                </td>

            </tr>

        <?php endif; ?>

        </tbody>

    </table>

</div>

</main>

</div>

<!-- =========================================================
     MODAL
========================================================= -->

<div class="modal"
     id="noteModal">

    <div class="modal-content">

        <div class="modal-header">

            <h2>
                Ajouter une Note
            </h2>

            <button class="close-btn"
                    onclick="closeModal()">

                ✕

            </button>

        </div>

        <form method="POST"
              class="premium-form">

            <div class="form-row">

                <div class="form-group">

                    <label>Étudiant</label>

                    <select name="etudiant_id"
                            class="form-control"
                            required>

                        <option value="">
                            Choisir un étudiant
                        </option>

                        <?php foreach($students as $student): ?>

                            <option value="<?= $student['id']; ?>">

                                <?= htmlspecialchars($student['nom']); ?>

                                <?= htmlspecialchars($student['prenom']); ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="form-group">

                    <label>Matière</label>

                    <select name="matiere_id"
                            class="form-control"
                            required>

                        <option value="">
                            Choisir une matière
                        </option>

                        <?php foreach($subjects as $subject): ?>

                            <option value="<?= $subject['id']; ?>">

                                <?= htmlspecialchars($subject['nom_matiere']); ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

            </div>

            <div class="form-row">

                <div class="form-group">

                    <label>Note</label>

                    <input type="number"
                           name="note"
                           step="0.01"
                           min="0"
                           max="20"

                           class="form-control"
                           required>

                </div>

                <div class="form-group">

                    <label>Session</label>

                    <select name="session"
                            class="form-control"
                            required>

                        <option value="Normale">Normale</option>
                        <option value="Rattrapage">Rattrapage</option>

                    </select>

                </div>

            </div>

            <div class="form-group">

                <label>Date</label>

                <input type="date"
                       name="date_note"

                       class="form-control"

                       value="<?= date('Y-m-d'); ?>">

            </div>

            <div class="modal-footer">

                <button type="submit"
                        name="add_note"
                        class="btn-primary">

                    Ajouter la Note

                </button>

            </div>

        </form>

    </div>

</div>

<script>

    lucide.createIcons();

    const modal = document.getElementById("noteModal");

    function openModal(){

        modal.style.display = "flex";

    }

    function closeModal(){

        modal.style.display = "none";

    }

    window.onclick = function(event){

        if(event.target == modal){

            modal.style.display = "none";

        }

    }

</script>

</body>
</html>