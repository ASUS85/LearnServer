<?php
session_start();
require_once '../config/database.php';
/* =========================
   FILIERES
========================= */

$filieres = $pdo->query("
    SELECT * FROM filieres
")->fetchAll();

/* =========================
   NIVEAUX
========================= */

$niveaux = $pdo->query("
    SELECT * FROM niveaux
")->fetchAll();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

/* =========================
   RECUPERATION ETUDIANTS
========================= */
$stmt = $pdo->query("
    SELECT 
        etudiants.*,
        filieres.nom_filiere,
        niveaux.nom_niveau
    FROM etudiants
    LEFT JOIN filieres 
        ON etudiants.filiere_id = filieres.id
    LEFT JOIN niveaux 
        ON etudiants.niveau_id = niveaux.id
    ORDER BY etudiants.id DESC
");
/* =========================
   AJOUT ETUDIANT
========================= */

if(isset($_POST['add_student'])){

    $matricule = $_POST['matricule'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $filiere_id = $_POST['filiere_id'];
    $niveau_id = $_POST['niveau_id'];

    $insert = $pdo->prepare("
        INSERT INTO etudiants(
            matricule,
            nom,
            prenom,
            email,
            filiere_id,
            niveau_id
        )
        VALUES(?,?,?,?,?,?)
    ");

    $insert->execute([
        $matricule,
        $nom,
        $prenom,
        $email,
        $filiere_id,
        $niveau_id
    ]);

    header("Location: students.php");
    exit;
}
/* =========================
   UPDATE ETUDIANT
========================= */

if(isset($_POST['update_student'])){

    $id = $_POST['student_id'];
    $matricule = $_POST['matricule'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $filiere_id = $_POST['filiere_id'];
    $niveau_id = $_POST['niveau_id'];

    $update = $pdo->prepare("
        UPDATE etudiants
        SET
            matricule = ?,
            nom = ?,
            prenom = ?,
            email = ?,
            filiere_id = ?,
            niveau_id = ?
        WHERE id = ?
    ");

    $update->execute([
        $matricule,
        $nom,
        $prenom,
        $email,
        $filiere_id,
        $niveau_id,
        $id
    ]);

    header("Location: students.php");
    exit;
}
/* =========================
   SUPPRESSION ETUDIANT
========================= */

if(isset($_GET['delete'])){

    $id = $_GET['delete'];

    $delete = $pdo->prepare("
        DELETE FROM etudiants
        WHERE id = ?
    ");

    $delete->execute([$id]);

    header("Location: students.php");
    exit;
}

$students = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Étudiants - EduManage</title>

    <!-- Assure-toi que ce fichier CSS contient les styles du dashboard premium -->
    <link rel="stylesheet" href="../assets/css/students.css">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
    function openAddModal(){
        document.getElementById("addModal").style.display = "flex";
    }

    function closeAddModal(){
        document.getElementById("addModal").style.display = "none";
    }

    function openEditModal(id, matricule, nom, prenom, email, filiere, niveau){
        document.getElementById("editModal").style.display = "flex";
        document.getElementById("edit_id").value = id;
        document.getElementById("edit_matricule").value = matricule;
        document.getElementById("edit_nom").value = nom;
        document.getElementById("edit_prenom").value = prenom;
        document.getElementById("edit_email").value = email;
        document.getElementById("edit_filiere").value = filiere;
        document.getElementById("edit_niveau").value = niveau;
    }

    function closeEditModal(){
        document.getElementById("editModal").style.display = "none";
    }
    </script>
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

<div class="app-container">

     <!-- =========================
         SIDEBAR
    ========================== -->

    <aside class="sidebar">

        <!-- TOP -->

        <div>

            <div class="brand">

                <div class="logo-box">
                    🎓
                </div>

                <div>

                    <h2 class="logo">
                        EduManage
                    </h2>

                    <span class="logo-subtitle">
                        Academic Prestige
                    </span>

                </div>

            </div>

            <!-- MENU -->

            <ul class="menu">

                <li class="menu-item">

                    <a href="dashboard.php">

                        <i data-lucide="layout-dashboard"></i>

                        <span>
                            Dashboard
                        </span>

                    </a>

                </li>

                <li class="menu-item active">

                    <a href="students.php">

                        <i data-lucide="graduation-cap"></i>

                        <span>
                            Étudiants
                        </span>

                    </a>

                </li>

                <li class="menu-item">

                    <a href="teachers.php">

                        <i data-lucide="users"></i>

                        <span>
                            Enseignants
                        </span>

                    </a>

                </li>

                <li class="menu-item">

                    <a href="subjects.php">

                        <i data-lucide="book-open"></i>

                        <span>
                            Matières
                        </span>

                    </a>

                </li>

                <li class="menu-item">

                    <a href="schedule.php">

                        <i data-lucide="calendar-days"></i>

                        <span>
                            Emploi du Temps
                        </span>

                    </a>

                </li>

                <li class="menu-item">

                    <a href="notes.php">

                        <i data-lucide="clipboard-list"></i>

                        <span>
                            Notes
                        </span>

                    </a>

                </li>

                <li class="menu-item">

                    <a href="settings.php">

                        <i data-lucide="settings"></i>

                        <span>
                            Paramètres
                        </span>

                    </a>

                </li>

            </ul>

        </div>

        <!-- BOTTOM -->

        <div class="sidebar-footer">

            <div class="admin-profile">

                <div class="avatar">
                    A
                </div>

                <div>

                    <h4>
                        <?php echo $_SESSION['user_nom']; ?>
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

    <!-- =========================
         MAIN CONTENT
    ========================== -->
    <main class="main-content">

        <!-- HEADER -->
        <header class="page-header">
            <div class="header-info">
                <div class="badge-accent">Administration</div>
                <h1>Gestion des Étudiants</h1>
                <p>Gérez les comptes, les filières et les niveaux de vos étudiants.</p>
            </div>
            
            <div class="header-actions">
                <button class="btn-primary" onclick="openAddModal()">
                    <i data-lucide="plus"></i> Ajouter un Étudiant
                </button>
            </div>
        </header>

        <!-- STATISTICS GRID -->
        <div class="stats-grid">
            <div class="stat-card glass-panel">
                <div class="stat-icon blue">
                    <i data-lucide="users"></i>
                </div>
                <div class="stat-details">
                    <h3>Total Étudiants</h3>
                    <p class="stat-number"><?php echo count($students); ?></p>
                </div>
            </div>

            <div class="stat-card glass-panel">
                <div class="stat-icon purple">
                    <i data-lucide="layers"></i>
                </div>
                <div class="stat-details">
                    <h3>Filières Actives</h3>
                    <!-- On peut utiliser count($filieres) pour rendre ça dynamique -->
                    <p class="stat-number"><?php echo count($filieres); ?></p>
                </div>
            </div>

            <div class="stat-card glass-panel">
                <div class="stat-icon orange">
                    <i data-lucide="trending-up"></i>
                </div>
                <div class="stat-details">
                    <h3>Niveaux</h3>
                    <!-- Idem pour les niveaux -->
                    <p class="stat-number"><?php echo count($niveaux); ?></p>
                </div>
            </div>
        </div>

        <!-- TABLE SECTION -->
        <div class="table-container glass-panel mt-4">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>Étudiant</th>
                        <th>Email</th>
                        <th>Filière</th>
                        <th>Niveau</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($students as $student): ?>
                    <tr>
                        <td><span class="badge-outline"><?php echo $student['matricule']; ?></span></td>
                        <td>
                            <div class="user-info">
                                <div class="avatar"><?php echo substr($student['nom'], 0, 1) . substr($student['prenom'], 0, 1); ?></div>
                                <div>
                                    <div class="fw-bold text-white"><?php echo $student['nom'] . ' ' . $student['prenom']; ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="text-muted"><?php echo $student['email']; ?></td>
                        <td><span class="badge-filiere"><?php echo $student['nom_filiere']; ?></span></td>
                        <td><span class="badge-niveau"><?php echo $student['nom_niveau']; ?></span></td>
                        
                        <td class="actions-cell text-right">
                            <button 
                                class="btn-icon edit" 
                                title="Modifier"
                                onclick="openEditModal(
                                    '<?php echo $student['id']; ?>',
                                    '<?php echo $student['matricule']; ?>',
                                    '<?php echo $student['nom']; ?>',
                                    '<?php echo $student['prenom']; ?>',
                                    '<?php echo $student['email']; ?>',
                                    '<?php echo $student['filiere_id']; ?>',
                                    '<?php echo $student['niveau_id']; ?>'
                                )"
                            >
                                <i data-lucide="edit-2"></i>
                            </button>
                            
                            <a 
                                href="students.php?delete=<?php echo $student['id']; ?>"
                                class="btn-icon delete"
                                title="Supprimer"
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet étudiant ?')"
                            >
                                <i data-lucide="trash-2"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </main>

</div>

<!-- =========================
     MODAL AJOUT ETUDIANT
========================== -->
<div class="modal premium-modal" id="addModal">
    <div class="modal-content glass-panel">
        <div class="modal-header">
            <h2>Ajouter un Étudiant</h2>
            <button class="close-btn" onclick="closeAddModal()">
                <i data-lucide="x"></i>
            </button>
        </div>

        <form method="POST" class="premium-form">
            <div class="form-row">
                <div class="form-group">
                    <label>Matricule</label>
                    <input type="text" name="matricule" class="form-control" required placeholder="Ex: 24SGR001">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required placeholder="email@domaine.com">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" class="form-control" required placeholder="Nom de famille">
                </div>
                <div class="form-group">
                    <label>Prénom</label>
                    <input type="text" name="prenom" class="form-control" required placeholder="Prénom">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                   <label>Filière</label>
                    <select name="filiere_id" class="form-control" required>
                        <option value="" disabled selected>-- Choisir une filière --</option>
                        <?php foreach($filieres as $filiere): ?>
                            <option value="<?php echo $filiere['id']; ?>"><?php echo $filiere['nom_filiere']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Niveau</label>
                    <select name="niveau_id" class="form-control" required>
                        <option value="" disabled selected>-- Choisir un niveau --</option>
                        <?php foreach($niveaux as $niveau): ?>
                            <option value="<?php echo $niveau['id']; ?>"><?php echo $niveau['nom_niveau']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeAddModal()">Annuler</button>
                <button type="submit" name="add_student" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- =========================
     MODAL MODIFIER
========================== -->
<div class="modal premium-modal" id="editModal">
    <div class="modal-content glass-panel">
        <div class="modal-header">
            <h2>Modifier l'Étudiant</h2>
            <button class="close-btn" onclick="closeEditModal()">
                <i data-lucide="x"></i>
            </button>
        </div>

        <form method="POST" class="premium-form">
            <input type="hidden" name="student_id" id="edit_id">

            <div class="form-row">
                <div class="form-group">
                    <label>Matricule</label>
                    <input type="text" name="matricule" id="edit_matricule" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="edit_email" class="form-control" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" id="edit_nom" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Prénom</label>
                    <input type="text" name="prenom" id="edit_prenom" class="form-control" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Filière</label>
                    <select name="filiere_id" id="edit_filiere" class="form-control" required>
                        <?php foreach($filieres as $filiere): ?>
                            <option value="<?php echo $filiere['id']; ?>"><?php echo $filiere['nom_filiere']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Niveau</label>
                    <select name="niveau_id" id="edit_niveau" class="form-control" required>
                        <?php foreach($niveaux as $niveau): ?>
                            <option value="<?php echo $niveau['id']; ?>"><?php echo $niveau['nom_niveau']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeEditModal()">Annuler</button>
                <button type="submit" name="update_student" class="btn-primary">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>

<!-- Initialisation des icônes Lucide -->
<script>
    lucide.createIcons();
</script>

</body>
</html>