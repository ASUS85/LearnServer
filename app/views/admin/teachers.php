<?php
/**
 * Vue Gestion Enseignants
 */
$teachers = $teachers ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Enseignants - EduManage</title>
    <link rel="stylesheet" href="<?php echo assetUrl('css/teachers.css'); ?>">
    <link rel="stylesheet" href="<?php echo assetUrl('css/dashboardadmin.css'); ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
<div class="floating-elements">
    <div class="floating book"></div>
    <div class="floating graduation"></div>
    <div class="floating pencil"></div>
    <div class="floating atom"></div>
    <div class="floating globe"></div>
</div>

<div class="app-container">
    <?php include basePath('app/views/layouts/admin_sidebar.php'); ?>

    <main class="main-content">
        <section class="section-header">
            <div class="header-content">
                <h1><i data-lucide="users"></i> Gestion des Enseignants</h1>
                <p>Gérez les profils des enseignants</p>
            </div>
            <button class="btn-primary" onclick="openAddModal()">
                <i data-lucide="plus"></i> Ajouter Enseignant
            </button>
        </section>

        <section class="section-content">
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Matricule</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($teachers)): ?>
                            <?php foreach($teachers as $teacher): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($teacher['matricule']); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['prenom']); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['telephone'] ?? '-'); ?></td>
                                    <td class="actions">
                                        <button class="btn-small btn-edit" onclick="openEditModal(<?php 
                                            echo $teacher['id'] . ', ';
                                            echo "'" . htmlspecialchars($teacher['matricule']) . "', ";
                                            echo "'" . htmlspecialchars($teacher['nom']) . "', ";
                                            echo "'" . htmlspecialchars($teacher['prenom']) . "', ";
                                            echo "'" . htmlspecialchars($teacher['email']) . "', ";
                                            echo "'" . htmlspecialchars($teacher['telephone'] ?? '') . "'";
                                        ?>)"><i data-lucide="edit"></i></button>
                                        <button class="btn-small btn-delete" onclick="deleteTeacher(<?php echo $teacher['id']; ?>)">
                                            <i data-lucide="trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align: center; padding: 20px;">Aucun enseignant enregistré</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<!-- MODALS -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Ajouter Enseignant</h2>
            <button class="btn-close" onclick="closeAddModal()">✕</button>
        </div>
        <form method="POST" class="form">
            <div class="form-group">
                <label>Nom</label>
                <input type="text" name="nom" required>
            </div>
            <div class="form-group">
                <label>Prénom</label>
                <input type="text" name="prenom" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Téléphone</label>
                <input type="text" name="telephone">
            </div>
            <div class="form-group">
                <label>Mot de passe par défaut</label>
                <input type="text" value="<?php echo htmlspecialchars(defaultUserPassword()); ?>" readonly>
                <small>Ce mot de passe est attribué automatiquement au nouvel utilisateur.</small>
                <input type="hidden" name="mot_de_passe" value="<?php echo htmlspecialchars(defaultUserPassword()); ?>">
            </div>
            <div class="form-group">
                <button type="submit" name="add_teacher" class="btn-primary">Ajouter</button>
                <button type="button" class="btn-secondary" onclick="closeAddModal()">Annuler</button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Modifier Enseignant</h2>
            <button class="btn-close" onclick="closeEditModal()">✕</button>
        </div>
        <form method="POST" class="form">
            <input type="hidden" id="edit_teacher_id" name="teacher_id">
            <div class="form-group">
                <label>Matricule</label>
                <input type="text" id="edit_matricule" readonly>
            </div>
            <div class="form-group">
                <label>Nom</label>
                <input type="text" id="edit_nom" name="nom" required>
            </div>
            <div class="form-group">
                <label>Prénom</label>
                <input type="text" id="edit_prenom" name="prenom" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" id="edit_email" name="email" required>
            </div>
            <div class="form-group">
                <label>Téléphone</label>
                <input type="text" id="edit_telephone" name="telephone">
            </div>
            <div class="form-group">
                <button type="submit" name="update_teacher" class="btn-primary">Mettre à jour</button>
                <button type="button" class="btn-secondary" onclick="closeEditModal()">Annuler</button>
            </div>
        </form>
    </div>
</div>

<?php foreach($teachers as $teacher): ?>
    <form id="delete_form_<?php echo $teacher['id']; ?>" method="POST" style="display: none;">
        <input type="hidden" name="teacher_id" value="<?php echo $teacher['id']; ?>">
        <input type="hidden" name="delete_teacher" value="1">
    </form>
<?php endforeach; ?>

<script>
    function openAddModal(){ document.getElementById("addModal").style.display = "flex"; }
    function closeAddModal(){ document.getElementById("addModal").style.display = "none"; }
    function openEditModal(id, matricule, nom, prenom, email, telephone){
        document.getElementById("editModal").style.display = "flex";
        document.getElementById("edit_teacher_id").value = id;
        document.getElementById("edit_matricule").value = matricule;
        document.getElementById("edit_nom").value = nom;
        document.getElementById("edit_prenom").value = prenom;
        document.getElementById("edit_email").value = email;
        document.getElementById("edit_telephone").value = telephone;
    }
    function closeEditModal(){ document.getElementById("editModal").style.display = "none"; }
    function deleteTeacher(id){ if(confirm('Êtes-vous sûr ?')) document.getElementById('delete_form_' + id).submit(); }
    lucide.createIcons();
</script>
</body>
</html>
