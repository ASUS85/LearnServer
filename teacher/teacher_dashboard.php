<?php

session_start();

if(
    !isset($_SESSION['user_role']) ||
    $_SESSION['user_role'] != 'teacher'
){

    header("Location: ../index.php");
    exit;

}

require_once("../config/database.php");

/* =========================================================
   TEACHER INFO
========================================================= */

$teacher_id = $_SESSION['user_id'];

/* =========================================================
   TOTAL COURSES
========================================================= */

$coursesQuery = $pdo->prepare("

    SELECT COUNT(*) as total

    FROM emplois_temps

    WHERE enseignant_id = ?

");

$coursesQuery->execute([$teacher_id]);

$totalCourses = $coursesQuery->fetch(PDO::FETCH_ASSOC)['total'];

/* =========================================================
   TOTAL STUDENTS
========================================================= */

$studentsQuery = $pdo->prepare("

    SELECT COUNT(DISTINCT etudiants.id) as total

    FROM etudiants

    INNER JOIN notes
    ON notes.etudiant_id = etudiants.id

    INNER JOIN emplois_temps
    ON emplois_temps.matiere_id = notes.matiere_id

    WHERE emplois_temps.enseignant_id = ?

");

$studentsQuery->execute([$teacher_id]);

$totalStudents = $studentsQuery->fetch(PDO::FETCH_ASSOC)['total'];

/* =========================================================
   TOTAL NOTES
========================================================= */

$notesQuery = $pdo->prepare("

    SELECT COUNT(*) as total

    FROM notes

    INNER JOIN emplois_temps
    ON emplois_temps.matiere_id = notes.matiere_id

    WHERE emplois_temps.enseignant_id = ?

");

$notesQuery->execute([$teacher_id]);

$totalNotes = $notesQuery->fetch(PDO::FETCH_ASSOC)['total'];

/* =========================================================
   TODAY COURSES
========================================================= */

$jours = [

    "Sunday" => "Dimanche",
    "Monday" => "Lundi",
    "Tuesday" => "Mardi",
    "Wednesday" => "Mercredi",
    "Thursday" => "Jeudi",
    "Friday" => "Vendredi",
    "Saturday" => "Samedi"

];

$today = $jours[date("l")];

/* =========================================================
   TODAY SCHEDULE
========================================================= */

$scheduleQuery = $pdo->prepare("

    SELECT
        emplois_temps.*,
        matieres.nom_matiere,
        filieres.nom_filiere,
        niveaux.nom_niveau

    FROM emplois_temps

    INNER JOIN matieres
    ON matieres.id = emplois_temps.matiere_id

    INNER JOIN filieres
    ON filieres.id = emplois_temps.filiere_id

    INNER JOIN niveaux
    ON niveaux.id = emplois_temps.niveau_id

    WHERE
        emplois_temps.enseignant_id = ?
        AND emplois_temps.jour = ?

    ORDER BY heure_debut ASC

");

$scheduleQuery->execute([

    $teacher_id,
    $today

]);

$todayCourses = $scheduleQuery->fetchAll(PDO::FETCH_ASSOC);

/* =========================================================
   RECENT NOTES
========================================================= */

$recentNotesQuery = $pdo->prepare("

    SELECT
        notes.note,
        notes.session,
        notes.date_note,

        etudiants.nom,
        etudiants.prenom,

        matieres.nom_matiere

    FROM notes

    INNER JOIN etudiants
    ON etudiants.id = notes.etudiant_id

    INNER JOIN matieres
    ON matieres.id = notes.matiere_id

    INNER JOIN emplois_temps
    ON emplois_temps.matiere_id = notes.matiere_id

    WHERE emplois_temps.enseignant_id = ?

    ORDER BY notes.date_note DESC

    LIMIT 5

");

$recentNotesQuery->execute([$teacher_id]);

$recentNotes = $recentNotesQuery->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Dashboard Enseignant
    </title>

    <link rel="stylesheet"
          href="../assets/css/teacher_session_dashboard.css">

</head>

<body>

<!-- =========================================================
   FLOATING ELEMENTS
========================================================= -->

<div class="floating-elements">

    <div class="floating atom"></div>
    <div class="floating equation"></div>
    <div class="floating orbital"></div>
    <div class="floating wave"></div>
    <div class="floating grid"></div>

</div>

<!-- =========================================================
   APP CONTAINER
========================================================= -->

<div class="app-container">

    <!-- SIDEBAR -->

    <?php include("sidebar_teacher.php"); ?>

    <!-- =====================================================
       MAIN CONTENT
    ====================================================== -->

    <main class="main-content">

        <!-- HEADER -->

        <div class="page-header">

            <div class="header-info">

                <span class="badge-accent">
                    Teacher Portal
                </span>

                <h1>
                    Bonjour,
                    <?= htmlspecialchars($_SESSION['user_nom']); ?>
                    👋
                </h1>

                <p>
                    Tableau de bord académique intelligent
                </p>

            </div>

            <div class="header-actions">

                <button class="btn-primary">

                    + Nouveau Support

                </button>

            </div>

        </div>

        <!-- =====================================================
           STATS
        ====================================================== -->

        <div class="stats-grid">

            <!-- CARD -->

            <div class="stat-card glass-panel">

                <div class="stat-icon blue">
                    📚
                </div>

                <div class="stat-details">

                    <h3>
                        Cours Assignés
                    </h3>

                    <div class="stat-number">
                        <?= $totalCourses; ?>
                    </div>

                </div>

            </div>

            <!-- CARD -->

            <div class="stat-card glass-panel">

                <div class="stat-icon purple">
                    👨‍🎓
                </div>

                <div class="stat-details">

                    <h3>
                        Étudiants
                    </h3>

                    <div class="stat-number">
                        <?= $totalStudents; ?>
                    </div>

                </div>

            </div>

            <!-- CARD -->

            <div class="stat-card glass-panel">

                <div class="stat-icon cyan">
                    📝
                </div>

                <div class="stat-details">

                    <h3>
                        Notes Publiées
                    </h3>

                    <div class="stat-number">
                        <?= $totalNotes; ?>
                    </div>

                </div>

            </div>

            <!-- CARD -->

            <div class="stat-card glass-panel">

                <div class="stat-icon orange">
                    📅
                </div>

                <div class="stat-details">

                    <h3>
                        Cours Aujourd’hui
                    </h3>

                    <div class="stat-number">
                        <?= count($todayCourses); ?>
                    </div>

                </div>

            </div>

        </div>

        <!-- =====================================================
           DASHBOARD GRID
        ====================================================== -->

        <div class="dashboard-grid">

            <!-- =================================================
               TODAY SCHEDULE
            ================================================== -->

            <div class="glass-panel schedule-panel">

                <div class="panel-header">

                    <h2>
                        Emploi du Temps du Jour
                    </h2>

                    <span class="live-badge">
                        <?= $today; ?>
                    </span>

                </div>

                <?php if(count($todayCourses) > 0): ?>

                    <div class="schedule-list">

                        <?php foreach($todayCourses as $course): ?>

                            <div class="schedule-item">

                                <div class="schedule-time">

                                    <?= substr($course['heure_debut'],0,5); ?>

                                    -

                                    <?= substr($course['heure_fin'],0,5); ?>

                                </div>

                                <div class="schedule-info">

                                    <h3>
                                        <?= htmlspecialchars($course['nom_matiere']); ?>
                                    </h3>

                                    <p>

                                        <?= htmlspecialchars($course['nom_filiere']); ?>

                                        •

                                        <?= htmlspecialchars($course['nom_niveau']); ?>

                                    </p>

                                </div>

                                <div class="schedule-room">

                                    <?= htmlspecialchars($course['salle']); ?>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php else: ?>

                    <div class="empty-state">

                        Aucun cours prévu aujourd’hui

                    </div>

                <?php endif; ?>

            </div>

            <!-- =================================================
               RECENT NOTES
            ================================================== -->

            <div class="glass-panel notes-panel">

                <div class="panel-header">

                    <h2>
                        Dernières Notes
                    </h2>

                </div>

                <div class="notes-list">

                    <?php foreach($recentNotes as $note): ?>

                        <div class="note-item">

                            <div class="note-avatar">

                                <?= strtoupper(substr($note['nom'],0,1)); ?>

                            </div>

                            <div class="note-content">

                                <h4>

                                    <?= htmlspecialchars($note['nom']); ?>

                                    <?= htmlspecialchars($note['prenom']); ?>

                                </h4>

                                <p>

                                    <?= htmlspecialchars($note['nom_matiere']); ?>

                                    •

                                    <?= htmlspecialchars($note['session']); ?>

                                </p>

                            </div>

                            <div class="note-grade">

                                <?= $note['note']; ?>/20

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            </div>

        </div>

        <!-- =====================================================
           ANALYTICS SECTION
        ====================================================== -->

        <div class="analytics-grid">

            <!-- PERFORMANCE -->

            <div class="glass-panel analytics-card">

                <div class="panel-header">

                    <h2>
                        Performance Générale
                    </h2>

                </div>

                <div class="progress-circle">

                    <div class="circle-value">
                        92%
                    </div>

                </div>

                <p class="analytics-text">

                    Niveau global des performances étudiantes

                </p>

            </div>

            <!-- ACTIVITY -->

            <div class="glass-panel analytics-card">

                <div class="panel-header">

                    <h2>
                        Activité Récente
                    </h2>

                </div>

                <div class="activity-list">

                    <div class="activity-item">

                        <span class="activity-dot blue"></span>

                        Nouvelle note enregistrée

                    </div>

                    <div class="activity-item">

                        <span class="activity-dot purple"></span>

                        Emploi du temps mis à jour

                    </div>

                    <div class="activity-item">

                        <span class="activity-dot cyan"></span>

                        Support ajouté

                    </div>

                </div>

            </div>

        </div>

    </main>

</div>

</body>
</html>