<?php
session_start();

if (!isset($_SESSION['user_role'])) {

    header("Location: ../index.php");
    exit;

}

require_once("../config/database.php");

$message = "";
$error = "";

/* =========================
   AJOUT EMPLOI DE TEMPS
========================= */

if(isset($_POST['add_schedule'])){

    $matiere_id = intval($_POST['matiere_id']);
    $enseignant_id = intval($_POST['enseignant_id']);
    $filiere_id = intval($_POST['filiere_id']);
    $niveau_id = intval($_POST['niveau_id']);

    $jour = trim($_POST['jour']);

    $heure_debut = $_POST['heure_debut'];
    $heure_fin = $_POST['heure_fin'];

    $salle = trim($_POST['salle']);

    $type_cours = trim($_POST['type_cours']);

    $semestre = trim($_POST['semestre']);

    if(
        !empty($matiere_id) &&
        !empty($enseignant_id) &&
        !empty($filiere_id) &&
        !empty($niveau_id) &&
        !empty($jour) &&
        !empty($heure_debut) &&
        !empty($heure_fin)
    ){

        $sql = "
            INSERT INTO emplois_temps(

                matiere_id,
                enseignant_id,
                filiere_id,
                niveau_id,
                jour,
                heure_debut,
                heure_fin,
                salle,
                type_cours,
                semestre

            )

            VALUES(?,?,?,?,?,?,?,?,?,?)
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([

            $matiere_id,
            $enseignant_id,
            $filiere_id,
            $niveau_id,
            $jour,
            $heure_debut,
            $heure_fin,
            $salle,
            $type_cours,
            $semestre

        ]);

        $message = "Emploi du temps ajouté avec succès.";

    }else{

        $error = "Veuillez remplir tous les champs.";

    }

}

/* =========================
   SUPPRESSION
========================= */

if(isset($_GET['delete']) && is_numeric($_GET['delete'])){

    $id = intval($_GET['delete']);

    $sql = "
        DELETE FROM emplois_temps
        WHERE id = ?
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([$id]);

    $message = "Cours supprimé avec succès.";

}

/* =========================
   RECUPERATION DONNEES
========================= */

$matieres = $pdo->query("
    SELECT *
    FROM matieres
    ORDER BY nom_matiere ASC
")->fetchAll(PDO::FETCH_ASSOC);

$enseignants = $pdo->query("
    SELECT *
    FROM enseignants
    ORDER BY nom ASC
")->fetchAll(PDO::FETCH_ASSOC);

$filieres = $pdo->query("
    SELECT *
    FROM filieres
    ORDER BY nom_filiere ASC
")->fetchAll(PDO::FETCH_ASSOC);

$niveaux = $pdo->query("
    SELECT *
    FROM niveaux
    ORDER BY nom_niveau ASC
")->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   RECUPERATION EMPLOIS
========================= */

$schedules = $pdo->query("

    SELECT

        emplois_temps.*,

        matieres.nom_matiere,
        matieres.code_matiere,

        enseignants.nom,
        enseignants.prenom,

        filieres.nom_filiere,

        niveaux.nom_niveau

    FROM emplois_temps

    INNER JOIN matieres
    ON emplois_temps.matiere_id = matieres.id

    INNER JOIN enseignants
    ON emplois_temps.enseignant_id = enseignants.id

    INNER JOIN filieres
    ON emplois_temps.filiere_id = filieres.id

    INNER JOIN niveaux
    ON emplois_temps.niveau_id = niveaux.id

    ORDER BY FIELD(
        jour,
        'Lundi',
        'Mardi',
        'Mercredi',
        'Jeudi',
        'Vendredi',
        'Samedi'
    ),
    heure_debut ASC

")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Gestion Emploi du Temps</title>

    <link rel="stylesheet"
          href="../assets/css/schedule.css">

    <script src="https://unpkg.com/lucide@latest"></script>

</head>

<body>

<!-- =========================
     SIDEBAR
========================= -->

<aside class="sidebar">

    <div>

        <h2 class="logo">
            EduManage
        </h2>

        <ul class="menu">

            <li class="menu-item">
                <a href="dashboard.php">
                    <span>🏠</span>
                    Dashboard
                </a>
            </li>

            <li class="menu-item">
                <a href="students.php">
                    <span>🎓</span>
                    Étudiants
                </a>
            </li>

            <li class="menu-item">
                <a href="teachers.php">
                    <span>👨‍🏫</span>
                    Enseignants
                </a>
            </li>

            <li class="menu-item">
                <a href="subjects.php">
                    <span>📚</span>
                    Matières
                </a>
            </li>

            <li class="menu-item active">
                <a href="schedule.php">
                    <span>📅</span>
                    Emploi du Temps
                </a>
            </li>
            <li class="menu-item">
                    <a href="settings.php">
                        <span>⚙️</span>
                        Paramètres
                    </a>
            </li>

        </ul>

    </div>

    <a href="../api/logout.php"
       class="logout-btn">

        🚪 Déconnexion

    </a>

</aside>

<!-- =========================
     MAIN CONTENT
========================= -->

<main class="main-content">

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

    <!-- TOPBAR -->

    <div class="topbar">

        <div>

            <h1>
                Gestion Emploi du Temps
            </h1>

            <p>
                Organisation académique intelligente
            </p>

        </div>

        <button class="add-btn"
                onclick="openModal()">

            <i data-lucide="plus"></i>

            Ajouter Cours

        </button>

    </div>

    <!-- STATS -->

    <div class="stats-grid">

        <div class="stat-card">

            <h3>Total Cours</h3>

            <p>
                <?= count($schedules); ?>
            </p>

        </div>

        <div class="stat-card">

            <h3>Filières</h3>

            <p>
                <?= count($filieres); ?>
            </p>

        </div>

        <div class="stat-card">

            <h3>Enseignants</h3>

            <p>
                <?= count($enseignants); ?>
            </p>

        </div>

        <div class="stat-card">

            <h3>Matières</h3>

            <p>
                <?= count($matieres); ?>
            </p>

        </div>

    </div>

    <!-- TABLE -->

    <div class="table-card">

        <table>

            <thead>

                <tr>

                    <th>Jour</th>
                    <th>Horaire</th>
                    <th>Matière</th>
                    <th>Enseignant</th>
                    <th>Filière</th>
                    <th>Niveau</th>
                    <th>Salle</th>
                    <th>Type</th>
                    <th>Semestre</th>
                    <th>Actions</th>

                </tr>

            </thead>

            <tbody>

            <?php if(count($schedules) > 0): ?>

                <?php foreach($schedules as $schedule): ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars($schedule['jour']); ?>
                        </td>

                        <td>

                            <?= substr($schedule['heure_debut'],0,5); ?>

                            -

                            <?= substr($schedule['heure_fin'],0,5); ?>

                        </td>

                        <td>

                            <strong>
                                <?= htmlspecialchars($schedule['nom_matiere']); ?>
                            </strong>

                            <br>

                            <small>
                                <?= htmlspecialchars($schedule['code_matiere']); ?>
                            </small>

                        </td>

                        <td>

                            <?= htmlspecialchars($schedule['nom']); ?>

                            <?= htmlspecialchars($schedule['prenom']); ?>

                        </td>

                        <td>
                            <?= htmlspecialchars($schedule['nom_filiere']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($schedule['nom_niveau']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($schedule['salle']); ?>
                        </td>

                        <td>

                            <span class="badge">

                                <?= htmlspecialchars($schedule['type_cours']); ?>

                            </span>

                        </td>

                        <td>
                            <?= htmlspecialchars($schedule['semestre']); ?>
                        </td>

                        <td class="actions">

                            <a
                                href="?delete=<?= $schedule['id']; ?>"
                                class="delete-btn"

                                onclick="return confirm('Supprimer ce cours ?')"
                            >

                                <i data-lucide="trash-2"></i>

                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>

                    <td colspan="10"
                        class="empty">

                        Aucun emploi du temps enregistré

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</main>

<!-- =========================
     MODAL
========================= -->

<div class="modal"
     id="scheduleModal">

    <div class="modal-content">

        <div class="modal-header">

            <h2>
                Ajouter un Cours
            </h2>

            <span class="close"
                  onclick="closeModal()">

                &times;

            </span>

        </div>

        <form method="POST">

            <div class="form-grid">

                <div class="form-group">

                    <label>Matière</label>

                    <select name="matiere_id" required>

                        <option value="">
                            Choisir une matière
                        </option>

                        <?php foreach($matieres as $matiere): ?>

                            <option value="<?= $matiere['id']; ?>">

                                <?= htmlspecialchars($matiere['nom_matiere']); ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="form-group">

                    <label>Enseignant</label>

                    <select name="enseignant_id" required>

                        <option value="">
                            Choisir un enseignant
                        </option>

                        <?php foreach($enseignants as $enseignant): ?>

                            <option value="<?= $enseignant['id']; ?>">

                                <?= htmlspecialchars($enseignant['nom']); ?>

                                <?= htmlspecialchars($enseignant['prenom']); ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="form-group">

                    <label>Filière</label>

                    <select name="filiere_id" required>

                        <?php foreach($filieres as $filiere): ?>

                            <option value="<?= $filiere['id']; ?>">

                                <?= htmlspecialchars($filiere['nom_filiere']); ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="form-group">

                    <label>Niveau</label>

                    <select name="niveau_id" required>

                        <?php foreach($niveaux as $niveau): ?>

                            <option value="<?= $niveau['id']; ?>">

                                <?= htmlspecialchars($niveau['nom_niveau']); ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="form-group">

                    <label>Jour</label>

                    <select name="jour" required>

                        <option>Lundi</option>
                        <option>Mardi</option>
                        <option>Mercredi</option>
                        <option>Jeudi</option>
                        <option>Vendredi</option>
                        <option>Samedi</option>

                    </select>

                </div>

                <div class="form-group">

                    <label>Type</label>

                    <select name="type_cours">

                        <option>Cours</option>
                        <option>TD</option>
                        <option>TP</option>
                        <option>Examen</option>

                    </select>

                </div>

                <div class="form-group">

                    <label>Heure Début</label>

                    <input type="time"
                           name="heure_debut"
                           required>

                </div>

                <div class="form-group">

                    <label>Heure Fin</label>

                    <input type="time"
                           name="heure_fin"
                           required>

                </div>

                <div class="form-group">

                    <label>Salle</label>

                    <input type="text"
                           name="salle"
                           required>

                </div>

                <div class="form-group">

                    <label>Semestre</label>

                    <input type="text"
                           name="semestre"
                           placeholder="Semestre 1">

                </div>

            </div>

            <button type="submit"
                    name="add_schedule"
                    class="save-btn">

                Ajouter le Cours

            </button>

        </form>

    </div>

</div>

<script>

    lucide.createIcons();

    const modal = document.getElementById("scheduleModal");

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