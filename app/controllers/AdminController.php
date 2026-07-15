<?php
/**
 * =====================================================
 * ADMIN CONTROLLER
 * =====================================================
 * Contrôle le tableau de bord administrateur
 */

class AdminController {

    private $pdo;
    private $admin;
    private $student;
    private $teacher;

    public function __construct($pdo) {
        AuthMiddleware::requireAdmin();
        $this->pdo = $pdo;
        $this->admin = new Admin($pdo);
        $this->student = new Student($pdo);
        $this->teacher = new Teacher($pdo);
    }

    /**
     * Dashboard
     */
    public function dashboard() {
        $stats = $this->admin->getStatistics();
        view('admin.dashboard', ['stats' => $stats]);
    }

    /**
     * Gestion des étudiants
     */
    public function students() {
        $students = $this->student->getAllWithDetails();

        $filieres = $this->pdo->query("SELECT * FROM filieres")->fetchAll();
        $niveaux = $this->pdo->query("SELECT * FROM niveaux")->fetchAll();

        view('admin.students', [
            'students' => $students,
            'filieres' => $filieres,
            'niveaux'  => $niveaux
        ]);
    }

    /**
     * Ajouter un étudiant
     */
    public function addStudent() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $data = [
            'nom'         => sanitize($_POST['nom'] ?? ''),
            'prenom'      => sanitize($_POST['prenom'] ?? ''),
            'email'       => sanitize($_POST['email'] ?? ''),
            'filiere_id'  => intval($_POST['filiere_id'] ?? 0),
            'niveau_id'   => intval($_POST['niveau_id'] ?? 0),
            'telephone'   => sanitize($_POST['telephone'] ?? '')
        ];

        if ($this->student->create($data)) {
            redirect('admin/students');
        }
    }

    /**
     * Modifier un étudiant
     */
    public function updateStudent() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $id = intval($_POST['student_id'] ?? 0);
        $data = [
            'nom'         => sanitize($_POST['nom'] ?? ''),
            'prenom'      => sanitize($_POST['prenom'] ?? ''),
            'email'       => sanitize($_POST['email'] ?? ''),
            'filiere_id'  => intval($_POST['filiere_id'] ?? 0),
            'niveau_id'   => intval($_POST['niveau_id'] ?? 0),
            'telephone'   => sanitize($_POST['telephone'] ?? '')
        ];

        if ($this->student->update($id, $data)) {
            redirect('admin/students');
        }
    }

    /**
     * Supprimer un étudiant
     */
    public function deleteStudent() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $id = intval($_POST['student_id'] ?? 0);
        if ($this->student->delete($id)) {
            redirect('admin/students');
        }
    }

    /**
     * Gestion des enseignants
     */
    public function teachers() {
        $teachers = $this->teacher->getAllWithDetails();
        view('admin.teachers', ['teachers' => $teachers]);
    }

    /**
     * Ajouter un enseignant
     */
    public function addTeacher() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $data = [
            'nom'       => sanitize($_POST['nom'] ?? ''),
            'prenom'    => sanitize($_POST['prenom'] ?? ''),
            'email'     => sanitize($_POST['email'] ?? ''),
            'telephone' => sanitize($_POST['telephone'] ?? '')
        ];

        if ($this->teacher->create($data)) {
            redirect('admin/teachers');
        }
    }

    /**
     * Modifier un enseignant
     */
    public function updateTeacher() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $id = intval($_POST['teacher_id'] ?? 0);
        $data = [
            'nom'       => sanitize($_POST['nom'] ?? ''),
            'prenom'    => sanitize($_POST['prenom'] ?? ''),
            'email'     => sanitize($_POST['email'] ?? ''),
            'telephone' => sanitize($_POST['telephone'] ?? '')
        ];

        if ($this->teacher->update($id, $data)) {
            redirect('admin/teachers');
        }
    }

    /**
     * Supprimer un enseignant
     */
    public function deleteTeacher() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $id = intval($_POST['teacher_id'] ?? 0);
        if ($this->teacher->delete($id)) {
            redirect('admin/teachers');
        }
    }

    /**
     * Gestion des matières
     */
    public function subjects() {
        try {
            $subjects = $this->pdo->query("
                SELECT m.*, NULL AS nom_classe
                FROM matieres m
                ORDER BY m.id DESC
            ")->fetchAll();
        } catch (PDOException $e) {
            $subjects = [];
        }

        // Conservé pour compatibilité de vue.
        $classes = [];

        view('admin.subjects', [
            'subjects' => $subjects,
            'classes'  => $classes
        ]);
    }

    /**
     * Gestion de l'emploi du temps
     */
    public function schedule() {
        try {
            $schedules = $this->pdo->query("
                SELECT
                    et.*,
                    m.nom_matiere,
                    e.nom,
                    e.prenom,
                    CONCAT(
                        COALESCE(f.nom_filiere, ''),
                        CASE WHEN f.nom_filiere IS NOT NULL AND n.nom_niveau IS NOT NULL THEN ' - ' ELSE '' END,
                        COALESCE(n.nom_niveau, '')
                    ) AS nom_classe
                FROM emplois_temps et
                LEFT JOIN matieres m ON et.matiere_id = m.id
                LEFT JOIN enseignants e ON et.enseignant_id = e.id
                LEFT JOIN filieres f ON et.filiere_id = f.id
                LEFT JOIN niveaux n ON et.niveau_id = n.id
                ORDER BY FIELD(et.jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'), et.heure_debut ASC
            ")->fetchAll();
        } catch (PDOException $e) {
            $schedules = [];
        }

        try {
            $matieres = $this->pdo->query("SELECT * FROM matieres")->fetchAll();
        } catch (PDOException $e) {
            $matieres = [];
        }

        try {
            $teachers = $this->teacher->getAll();
        } catch (PDOException $e) {
            $teachers = [];
        }

        // Conservé pour compatibilité de vue.
        $classes = [];

        view('admin.schedule', [
            'schedules' => $schedules,
            'matieres'  => $matieres,
            'teachers'  => $teachers,
            'classes'   => $classes
        ]);
    }

    /**
     * Gestion des notes
     */
    public function notes() {
        $notes = $this->pdo->query("
            SELECT n.*, e.nom, e.prenom, m.nom_matiere
            FROM notes n
            LEFT JOIN etudiants e ON n.etudiant_id = e.id
            LEFT JOIN matieres m ON n.matiere_id = m.id
        ")->fetchAll();

        view('admin.notes', ['notes' => $notes]);
    }

    /**
     * Paramètres
     */
    public function settings() {
        $adminId = $_SESSION['user_id'] ?? null;
        $adminData = $this->admin->findById($adminId);

        if (!$adminData) {
            session_destroy();
            redirect('index.php');
        }

        $message = '';
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
            $nom = sanitize($_POST['nom'] ?? '');
            $prenom = sanitize($_POST['prenom'] ?? '');
            $email = sanitize($_POST['email'] ?? '');
            $telephone = sanitize($_POST['telephone'] ?? '');

            if (empty($nom) || empty($prenom) || empty($email)) {
                $error = "Veuillez remplir tous les champs obligatoires.";
            } elseif (!isValidEmail($email)) {
                $error = "Email invalide.";
            } else {
                $check = $this->pdo->prepare("SELECT id FROM admins WHERE email = ? AND id != ?");
                $check->execute([$email, $adminId]);

                if ($check->fetch()) {
                    $error = "Cet email existe déjà.";
                } else {
                    $updated = $this->admin->update($adminId, [
                        'nom' => $nom,
                        'prenom' => $prenom,
                        'email' => $email,
                        'telephone' => $telephone
                    ]);

                    if ($updated) {
                        $_SESSION['user_nom'] = $nom;
                        $_SESSION['user_prenom'] = $prenom;
                        $_SESSION['user_email'] = $email;
                        $message = "Profil mis à jour avec succès.";
                    } else {
                        $error = "Impossible de mettre à jour le profil.";
                    }
                }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                $error = "Veuillez remplir tous les champs.";
            } elseif (!verifyPassword($currentPassword, $adminData['mot_de_passe']) && $currentPassword !== $adminData['mot_de_passe']) {
                $error = "Mot de passe actuel incorrect.";
            } elseif (strlen($newPassword) < 6) {
                $error = "Le mot de passe doit contenir au moins 6 caractères.";
            } elseif ($newPassword !== $confirmPassword) {
                $error = "Les mots de passe ne correspondent pas.";
            } else {
                $updated = $this->admin->update($adminId, ['mot_de_passe' => $newPassword]);
                if ($updated) {
                    $message = "Mot de passe modifié avec succès.";
                } else {
                    $error = "Impossible de modifier le mot de passe.";
                }
            }
        }

        $adminData = $this->admin->findById($adminId);

        view('admin.settings', [
            'admin' => $adminData,
            'message' => $message,
            'error' => $error
        ]);
    }
}
