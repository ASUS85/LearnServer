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
        $subjects = $this->pdo->query("
            SELECT m.*, c.nom_classe
            FROM matieres m
            LEFT JOIN classes c ON m.classe_id = c.id
        ")->fetchAll();

        $classes = $this->pdo->query("SELECT * FROM classes")->fetchAll();

        view('admin.subjects', [
            'subjects' => $subjects,
            'classes'  => $classes
        ]);
    }

    /**
     * Gestion de l'emploi du temps
     */
    public function schedule() {
        $schedules = $this->pdo->query("
            SELECT et.*, m.nom_matiere, e.nom, e.prenom, c.nom_classe
            FROM emplois_temps et
            LEFT JOIN matieres m ON et.matiere_id = m.id
            LEFT JOIN enseignants e ON et.enseignant_id = e.id
            LEFT JOIN classes c ON et.classe_id = c.id
        ")->fetchAll();

        $matieres = $this->pdo->query("SELECT * FROM matieres")->fetchAll();
        $teachers = $this->teacher->getAll();
        $classes = $this->pdo->query("SELECT * FROM classes")->fetchAll();

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
        view('admin.settings');
    }
}
