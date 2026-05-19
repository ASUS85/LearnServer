<?php
session_start();

if (!isset($_SESSION['user_role'])) {
    header("Location: ../index.php");
    exit;
}

require_once("../config/database.php");

$message = "";
$error = "";

// =========================
// AJOUT MATIERE
// =========================

if(isset($_POST['add_subject'])){

    $nom_matiere = trim($_POST['nom_matiere']);
    $code_matiere = trim($_POST['code_matiere']);
    $coefficient = trim($_POST['coefficient']);

    if(!empty($nom_matiere) && !empty($code_matiere)){

        $check = $pdo->prepare("SELECT id FROM matieres WHERE code_matiere = ?");
        $check->execute([$code_matiere]);

        if($check->rowCount() > 0){

            $error = "Ce code matière existe déjà.";

        }else{

            $sql = "INSERT INTO matieres(
                        nom_matiere,
                        code_matiere,
                        coefficient
                    )
                    VALUES(?,?,?)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                $nom_matiere,
                $code_matiere,
                $coefficient
            ]);

            $message = "Matière ajoutée avec succès.";

        }

    }else{

        $error = "Veuillez remplir tous les champs.";

    }

}

// =========================
// MODIFIER MATIERE
// =========================

if(isset($_POST['update_subject'])){

    $id = $_POST['subject_id'];

    $nom_matiere = trim($_POST['nom_matiere']);
    $code_matiere = trim($_POST['code_matiere']);
    $coefficient = trim($_POST['coefficient']);

    $sql = "UPDATE matieres SET
                nom_matiere = ?,
                code_matiere = ?,
                coefficient = ?
            WHERE id = ?";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $nom_matiere,
        $code_matiere,
        $coefficient,
        $id
    ]);

    $message = "Matière modifiée avec succès.";

}

// =========================
// SUPPRIMER MATIERE
// =========================

if(isset($_GET['delete'])){

    $id = $_GET['delete'];

    $sql = "DELETE FROM matieres WHERE id = ?";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([$id]);

    $message = "Matière supprimée avec succès.";

}

// =========================
// RECUPERATION MATIERES
// =========================

$stmt = $pdo->query("SELECT * FROM matieres ORDER BY id DESC");
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestion des Matières</title>

    <link rel="stylesheet" href="../../assets/css/subjects.css">

    <script src="https://unpkg.com/lucide@latest"></script>

</head>

<body>

           <!-- =========================
         SIDEBAR
    ========================== -->

    <?php include '../includes/sidebar.php'; ?>

    <!-- =========================
     MAIN CONTENT
========================= -->

<main class="main-content">

    <!-- PAGE HEADER -->

    <div class="page-header">

        <div class="header-info">

            <span class="badge-accent">
                Academic Management
            </span>

            <h1>
                Gestion des Matières
            </h1>

            <p>
                Administration des matières académiques et coefficients
            </p>

        </div>

        <button class="add-btn" onclick="openModal()">

            <i data-lucide="plus"></i>

            Ajouter Matière

        </button>

    </div>

    <!-- ALERTS -->

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

    <!-- STATS -->

    <div class="stats-grid">

        <div class="stat-card glass-panel">

            <div class="stat-icon blue">

                <i data-lucide="book-open"></i>

            </div>

            <div class="stat-details">

                <h3>
                    Total Matières
                </h3>

                <p class="stat-number">

                    <?= count($subjects); ?>

                </p>

            </div>

        </div>

        <div class="stat-card glass-panel">

            <div class="stat-icon purple">

                <i data-lucide="layers-3"></i>

            </div>

            <div class="stat-details">

                <h3>
                    Matières Actives
                </h3>

                <p class="stat-number">

                    <?= count($subjects); ?>

                </p>

            </div>

        </div>

        <div class="stat-card glass-panel">

            <div class="stat-icon orange">

                <i data-lucide="calculator"></i>

            </div>

            <div class="stat-details">

                <h3>
                    Coefficients
                </h3>

                <p class="stat-number">

                    <?=
                        array_sum(
                            array_column($subjects, 'coefficient')
                        );
                    ?>

                </p>

            </div>

        </div>

    </div>

    <!-- TABLE -->

    <div class="table-card glass-panel">

        <div class="table-container">

            <table class="premium-table">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Matière</th>
                        <th>Code</th>
                        <th>Coefficient</th>
                        <th class="text-right">Actions</th>

                    </tr>

                </thead>

                <tbody>

                    <?php if(count($subjects) > 0): ?>

                        <?php foreach($subjects as $subject): ?>

                            <tr>

                                <td>

                                    <span class="badge-outline">

                                        #<?= $subject['id']; ?>

                                    </span>

                                </td>

                                <td>

                                    <div class="user-info">

                                        <div class="avatar">

                                            <?= strtoupper(substr($subject['nom_matiere'],0,1)); ?>

                                        </div>

                                        <div>

                                            <div class="fw-bold text-white">

                                                <?= htmlspecialchars($subject['nom_matiere']); ?>

                                            </div>

                                            <div class="text-muted">

                                                Matière académique

                                            </div>

                                        </div>

                                    </div>

                                </td>

                                <td>

                                    <span class="badge">

                                        <?= htmlspecialchars($subject['code_matiere']); ?>

                                    </span>

                                </td>

                                <td>

                                    <span class="coef">

                                        <?= htmlspecialchars($subject['coefficient']); ?>

                                    </span>

                                </td>

                                <td>

                                    <div class="actions">

                                        <!-- EDIT -->

                                        <button
                                            class="edit-btn"

                                            onclick="openEditModal(
                                                '<?= $subject['id']; ?>',
                                                '<?= htmlspecialchars($subject['nom_matiere']); ?>',
                                                '<?= htmlspecialchars($subject['code_matiere']); ?>',
                                                '<?= htmlspecialchars($subject['coefficient']); ?>'
                                            )"
                                        >

                                            <i data-lucide="square-pen"></i>

                                        </button>

                                        <!-- DELETE -->

                                        <a
                                            href="?delete=<?= $subject['id']; ?>"
                                            class="delete-btn"

                                            onclick="return confirm('Voulez-vous supprimer cette matière ?')"
                                        >

                                            <i data-lucide="trash-2"></i>

                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="5" class="empty">

                                Aucune matière trouvée

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</main>

    <!-- =========================
         MODAL AJOUT
    ========================== -->

    <div class="modal" id="subjectModal">

        <div class="modal-content">

            <div class="modal-header">

                <h2>Ajouter Matière</h2>

                <span class="close" onclick="closeModal()">
                    &times;
                </span>

            </div>

            <form method="POST">

                <div class="form-group">

                    <label>Nom Matière</label>

                    <input
                        type="text"
                        name="nom_matiere"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Code Matière</label>

                    <input
                        type="text"
                        name="code_matiere"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Coefficient</label>

                    <input
                        type="number"
                        name="coefficient"
                        required
                    >

                </div>

                <button
                    type="submit"
                    name="add_subject"
                    class="save-btn"
                >

                    Ajouter Matière

                </button>

            </form>

        </div>

    </div>

    <!-- =========================
         MODAL MODIFICATION
    ========================== -->

    <div class="modal" id="editModal">

        <div class="modal-content">

            <div class="modal-header">

                <h2>Modifier Matière</h2>

                <span class="close" onclick="closeEditModal()">
                    &times;
                </span>

            </div>

            <form method="POST">

                <input
                    type="hidden"
                    name="subject_id"
                    id="edit_id"
                >

                <div class="form-group">

                    <label>Nom Matière</label>

                    <input
                        type="text"
                        name="nom_matiere"
                        id="edit_nom"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Code Matière</label>

                    <input
                        type="text"
                        name="code_matiere"
                        id="edit_code"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Coefficient</label>

                    <input
                        type="number"
                        name="coefficient"
                        id="edit_coefficient"
                        required
                    >

                </div>

                <button
                    type="submit"
                    name="update_subject"
                    class="save-btn"
                >

                    Modifier Matière

                </button>

            </form>

        </div>

    </div>

    <!-- =========================
         JAVASCRIPT
    ========================== -->

    <script>

        lucide.createIcons();

        // =========================
        // MODAL AJOUT
        // =========================

        const modal = document.getElementById("subjectModal");

        function openModal(){

            modal.style.display = "flex";

        }

        function closeModal(){

            modal.style.display = "none";

        }

        // =========================
        // MODAL EDIT
        // =========================

        const editModal = document.getElementById("editModal");

        function openEditModal(
            id,
            nom,
            code,
            coefficient
        ){

            editModal.style.display = "flex";

            document.getElementById("edit_id").value = id;
            document.getElementById("edit_nom").value = nom;
            document.getElementById("edit_code").value = code;
            document.getElementById("edit_coefficient").value = coefficient;

        }

        function closeEditModal(){

            editModal.style.display = "none";

        }

        // =========================
        // CLOSE OUTSIDE
        // =========================

        window.onclick = function(event){

            if(event.target == modal){

                modal.style.display = "none";

            }

            if(event.target == editModal){

                editModal.style.display = "none";

            }

        }

    </script>

</body>
</html>