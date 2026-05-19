<?php
/**
 * Vue Gestion Étudiants
 * Variables disponibles:
 * $students - Liste des étudiants avec détails (filière, niveau)
 * $filieres - Liste des filières
 * $niveaux  - Liste des niveaux
 */
?>
<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Gestion Étudiants - EduManage
    </title>

    <!-- CSS -->
    <link rel="stylesheet"
          href="<?php echo assetUrl('css/students.css'); ?>">
    <link rel="stylesheet"
          href="<?php echo assetUrl('css/dashboardadmin.css'); ?>">

    <!-- ICONS -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        function openAddModal(){
            document.getElementById("addModal").style.display = "flex";
        }

        function closeAddModal(){
            document.getElementById("addModal").style.display = "none";
        }

        function openEditModal(id, matricule, nom, prenom, email, filiere, niveau, telephone){
            document.getElementById("editModal").style.display = "flex";
            document.getElementById("edit_student_id").value = id;
            document.getElementById("edit_matricule").value = matricule;
            document.getElementById("edit_nom").value = nom;
            document.getElementById("edit_prenom").value = prenom;
            document.getElementById("edit_email").value = email;
            document.getElementById("edit_filiere_id").value = filiere;
            document.getElementById("edit_niveau_id").value = niveau;
            document.getElementById("edit_telephone").value = telephone;
        }

        function closeEditModal(){
            document.getElementById("editModal").style.display = "none";
        }

        function deleteStudent(id){
            if(confirm('Êtes-vous sûr de vouloir supprimer cet étudiant ?')){
                document.getElementById('delete_form_' + id).submit();
            }
        }
    </script>

</head>

<body>

<!-- FLOATING BACKGROUND -->
<div class="floating-elements">
    <div class="floating book"></div>
    <div class="floating graduation"></div>
    <div class="floating pencil"></div>
    <div class="floating atom"></div>
    <div class="floating globe"></div>
</div>

<div class="container">

    <!-- SIDEBAR -->
    <?php include basePath('app/views/layouts/admin_sidebar.php'); ?>

    <!-- MAIN CONTENT -->

    <main class="main-content">

        <!-- HEADER -->

        <section class="section-header">

            <div class="header-content">

                <h1>
                    <i data-lucide="graduation-cap"></i>
                    Gestion des Étudiants
                </h1>

                <p>
                    Gérez les profils et inscriptions des étudiants
                </p>

            </div>

            <button class="btn-primary"
                    onclick="openAddModal()">

                <i data-lucide="plus"></i>
                Ajouter Étudiant

            </button>

        </section>

        <!-- STUDENTS TABLE -->

        <section class="section-content">

            <div class="table-wrapper">

                <table class="data-table">

                    <thead>

                        <tr>

                            <th>Matricule</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Filière</th>
                            <th>Niveau</th>
                            <th>Téléphone</th>
                            <th>Actions</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if (!empty($students)): ?>

                            <?php foreach($students as $student): ?>

                                <tr>

                                    <td><?php echo htmlspecialchars($student['matricule']); ?></td>
                                    <td><?php echo htmlspecialchars($student['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($student['prenom']); ?></td>
                                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                                    <td><?php echo htmlspecialchars($student['nom_filiere'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($student['nom_niveau'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($student['telephone'] ?? '-'); ?></td>

                                    <td class="actions">

                                        <button class="btn-small btn-edit"
                                                onclick="openEditModal(<?php 
                                                    echo $student['id'] . ', ';
                                                    echo "'" . htmlspecialchars($student['matricule']) . "', ";
                                                    echo "'" . htmlspecialchars($student['nom']) . "', ";
                                                    echo "'" . htmlspecialchars($student['prenom']) . "', ";
                                                    echo "'" . htmlspecialchars($student['email']) . "', ";
                                                    echo ($student['filiere_id'] ?? 0) . ", ";
                                                    echo ($student['niveau_id'] ?? 0) . ", ";
                                                    echo "'" . htmlspecialchars($student['telephone'] ?? '') . "'";
                                                ?>)">

                                            <i data-lucide="edit"></i>

                                        </button>

                                        <button class="btn-small btn-delete"
                                                onclick="deleteStudent(<?php echo $student['id']; ?>)">

                                            <i data-lucide="trash"></i>

                                        </button>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="8" style="text-align: center; padding: 20px;">
                                    Aucun étudiant enregistré
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>

    </main>

</div>

<!-- ADD MODAL -->
<div id="addModal" class="modal">

    <div class="modal-content">

        <div class="modal-header">

            <h2>Ajouter Étudiant</h2>

            <button class="btn-close" onclick="closeAddModal()">
                ✕
            </button>

        </div>

        <form method="POST"
              class="form">

            <div class="form-group">
                <label>Nom</label>
                <input type="text"
                       name="nom"
                       required>
            </div>

            <div class="form-group">
                <label>Prénom</label>
                <input type="text"
                       name="prenom"
                       required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email"
                       name="email"
                       required>
            </div>

            <div class="form-group">
                <label>Téléphone</label>
                <input type="text"
                       name="telephone">
            </div>

            <div class="form-group">
                <label>Filière</label>
                <select name="filiere_id">
                    <option value="">Sélectionner...</option>
                    <?php foreach($filieres as $filiere): ?>
                        <option value="<?php echo $filiere['id']; ?>">
                            <?php echo htmlspecialchars($filiere['nom_filiere']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Niveau</label>
                <select name="niveau_id">
                    <option value="">Sélectionner...</option>
                    <?php foreach($niveaux as $niveau): ?>
                        <option value="<?php echo $niveau['id']; ?>">
                            <?php echo htmlspecialchars($niveau['nom_niveau']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">

                <button type="submit"
                        name="add_student"
                        class="btn-primary">

                    Ajouter

                </button>

                <button type="button"
                        class="btn-secondary"
                        onclick="closeAddModal()">

                    Annuler

                </button>

            </div>

        </form>

    </div>

</div>

<!-- EDIT MODAL -->
<div id="editModal" class="modal">

    <div class="modal-content">

        <div class="modal-header">

            <h2>Modifier Étudiant</h2>

            <button class="btn-close" onclick="closeEditModal()">
                ✕
            </button>

        </div>

        <form method="POST"
              class="form">

            <input type="hidden"
                   id="edit_student_id"
                   name="student_id">

            <div class="form-group">
                <label>Matricule</label>
                <input type="text"
                       id="edit_matricule"
                       readonly>
            </div>

            <div class="form-group">
                <label>Nom</label>
                <input type="text"
                       id="edit_nom"
                       name="nom"
                       required>
            </div>

            <div class="form-group">
                <label>Prénom</label>
                <input type="text"
                       id="edit_prenom"
                       name="prenom"
                       required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email"
                       id="edit_email"
                       name="email"
                       required>
            </div>

            <div class="form-group">
                <label>Téléphone</label>
                <input type="text"
                       id="edit_telephone"
                       name="telephone">
            </div>

            <div class="form-group">
                <label>Filière</label>
                <select id="edit_filiere_id"
                        name="filiere_id">
                    <option value="">Sélectionner...</option>
                    <?php foreach($filieres as $filiere): ?>
                        <option value="<?php echo $filiere['id']; ?>">
                            <?php echo htmlspecialchars($filiere['nom_filiere']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Niveau</label>
                <select id="edit_niveau_id"
                        name="niveau_id">
                    <option value="">Sélectionner...</option>
                    <?php foreach($niveaux as $niveau): ?>
                        <option value="<?php echo $niveau['id']; ?>">
                            <?php echo htmlspecialchars($niveau['nom_niveau']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">

                <button type="submit"
                        name="update_student"
                        class="btn-primary">

                    Mettre à jour

                </button>

                <button type="button"
                        class="btn-secondary"
                        onclick="closeEditModal()">

                    Annuler

                </button>

            </div>

        </form>

    </div>

</div>

<!-- DELETE FORMS -->
<?php foreach($students as $student): ?>
    <form id="delete_form_<?php echo $student['id']; ?>"
          method="POST"
          style="display: none;">
        <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
        <input type="hidden" name="delete_student" value="1">
    </form>
<?php endforeach; ?>

<!-- ICONS -->
<script>

    lucide.createIcons();

</script>

</body>
</html>
