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
// AJOUT ENSEIGNANT
// =========================
if(isset($_POST['add_teacher'])){
    $matricule = trim($_POST['matricule']);
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $mot_de_passe = trim($_POST['mot_de_passe']);
    $telephone = trim($_POST['telephone']);
    $specialite = trim($_POST['specialite']);

    if(!empty($matricule) && !empty($nom) && !empty($prenom) && !empty($email) && !empty($mot_de_passe)){
        $check = $pdo->prepare("SELECT id FROM enseignants WHERE email = ?");
        $check->execute([$email]);

        if($check->rowCount() > 0){
            $error = "Cet email existe déjà.";
        } else {
            $passwordHash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $sql = "INSERT INTO enseignants(matricule, nom, prenom, email, mot_de_passe, telephone, specialite) VALUES(?,?,?,?,?,?,?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$matricule, $nom, $prenom, $email, $passwordHash, $telephone, $specialite]);
            $message = "Enseignant ajouté avec succès.";
        }
    } else {
        $error = "Veuillez remplir tous les champs obligatoires.";
    }
}

// =========================
// MODIFIER ENSEIGNANT
// =========================
if(isset($_POST['update_teacher'])){
    $id = $_POST['teacher_id'];
    $matricule = trim($_POST['matricule']);
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $specialite = trim($_POST['specialite']);

    $sql = "UPDATE enseignants SET matricule = ?, nom = ?, prenom = ?, email = ?, telephone = ?, specialite = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$matricule, $nom, $prenom, $email, $telephone, $specialite, $id]);
    $message = "Enseignant modifié avec succès.";
}

// =========================
// SUPPRIMER ENSEIGNANT
// =========================
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $sql = "DELETE FROM enseignants WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $message = "Enseignant supprimé avec succès.";
}

// Récupération des enseignants
$stmt = $pdo->query("SELECT * FROM enseignants ORDER BY id DESC");
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Enseignants | EduManage</title>
    <!-- On réutilise le CSS premium pour la cohérence -->
    <link rel="stylesheet" href="../../assets/css/teachers.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

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

                <li class="menu-item">

                    <a href="students.php">

                        <i data-lucide="graduation-cap"></i>

                        <span>
                            Étudiants
                        </span>

                    </a>

                </li>

                <li class="menu-item active">

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
        <!-- MAIN CONTENT -->
        <main class="main-content">
            
            <!-- Notifications -->
            <?php if($message): ?>
                <div class="alert success-alert"><?= $message; ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert error-alert"><?= $error; ?></div>
            <?php endif; ?>

            <header class="page-header">
                <div class="header-info">
                    <span class="badge-accent">Administration</span>
                    <h1>Gestion des Enseignants</h1>
                    <p>Gérez les membres du corps professoral et leurs spécialités.</p>
                </div>
                <button class="btn-primary" onclick="openModal('teacherModal')">
                    <i data-lucide="user-plus"></i> Nouveau Professeur
                </button>
            </header>

            <!-- Liste des Enseignants -->
            <div class="glass-panel table-container">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th>Enseignant</th>
                            <th>Matricule</th>
                            <th>Contact</th>
                            <th>Spécialité</th>
                            <th>Date d'Arrivée</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($teachers as $t): ?>
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="avatar"><?= strtoupper(substr($t['nom'], 0, 1)) ?></div>
                                    <div>
                                        <div class="fw-bold text-white"><?= htmlspecialchars($t['nom'].' '.$t['prenom']) ?></div>
                                        <div class="text-muted"><?= htmlspecialchars($t['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge-outline"><?= htmlspecialchars($t['matricule']) ?></span></td>
                            <td><span class="text-muted"><?= htmlspecialchars($t['telephone']) ?></span></td>
                            <td><span class="badge-filiere"><?= htmlspecialchars($t['specialite']) ?></span></td>
                            <td><span class="text-muted"><?= date("d M Y", strtotime($t['created_at'])) ?></span></td>
                            <td class="actions-cell">
                                <button class="btn-icon edit" onclick="openEditModal(<?= htmlspecialchars(json_encode($t)) ?>)">
                                    <i data-lucide="edit-3"></i>
                                </button>
                                <a href="?delete=<?= $t['id'] ?>" class="btn-icon delete" onclick="return confirm('Supprimer cet enseignant ?')">
                                    <i data-lucide="trash-2"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($teachers)): ?>
                            <tr><td colspan="6" class="text-center text-muted">Aucun enseignant enregistré.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- MODAL AJOUT -->
    <div class="modal" id="teacherModal">
        <div class="modal-content glass-panel">
            <div class="modal-header">
                <h2>Ajouter un Enseignant</h2>
                <button class="close-btn" onclick="closeModal('teacherModal')"><i data-lucide="x"></i></button>
            </div>
            <form method="POST" class="premium-form">
                <div class="form-row">
                    <div class="form-group">
                        <label>Matricule</label>
                        <input type="text" name="matricule" class="form-control" placeholder="Ex: PROF-2026-001" required>
                    </div>
                    <div class="form-group">
                        <label>Spécialité</label>
                        <input type="text" name="specialite" class="form-control" placeholder="Ex: Mathématiques">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Nom</label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Prénom</label>
                        <input type="text" name="prenom" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Email Professionnel</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Téléphone</label>
                        <input type="text" name="telephone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Mot de passe</label>
                        <input type="password" name="mot_de_passe" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('teacherModal')">Annuler</button>
                    <button type="submit" name="add_teacher" class="btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT -->
    <div class="modal" id="editModal">
        <div class="modal-content glass-panel">
            <div class="modal-header">
                <h2>Modifier Enseignant</h2>
                <button class="close-btn" onclick="closeModal('editModal')"><i data-lucide="x"></i></button>
            </div>
            <form method="POST" class="premium-form">
                <input type="hidden" name="teacher_id" id="edit_id">
                <div class="form-row">
                    <div class="form-group">
                        <label>Matricule</label>
                        <input type="text" name="matricule" id="edit_matricule" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Spécialité</label>
                        <input type="text" name="specialite" id="edit_specialite" class="form-control">
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
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="edit_email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="text" name="telephone" id="edit_telephone" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('editModal')">Annuler</button>
                    <button type="submit" name="update_teacher" class="btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function openModal(id) {
            document.getElementById(id).style.display = "flex";
        }

        function closeModal(id) {
            document.getElementById(id).style.display = "none";
        }

        function openEditModal(teacher) {
            document.getElementById('edit_id').value = teacher.id;
            document.getElementById('edit_matricule').value = teacher.matricule;
            document.getElementById('edit_nom').value = teacher.nom;
            document.getElementById('edit_prenom').value = teacher.prenom;
            document.getElementById('edit_email').value = teacher.email;
            document.getElementById('edit_telephone').value = teacher.telephone;
            document.getElementById('edit_specialite').value = teacher.specialite;
            openModal('editModal');
        }

        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = "none";
            }
        }
    </script>
</body>
</html>