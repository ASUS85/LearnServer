<?php
/**
 * =====================================================
 * TEACHER CONTROLLER
 * =====================================================
 * Contrôle le tableau de bord enseignant
 */

class TeacherController {

    private $pdo;
    private $teacher;

    public function __construct($pdo) {
        AuthMiddleware::requireTeacher();
        $this->pdo = $pdo;
        $this->teacher = new Teacher($pdo);
    }

    /**
     * Dashboard enseignant
     */
    public function dashboard() {
        $teacherId = $_SESSION['user_id'];
        $teacher = $this->teacher->findById($teacherId);
        $classes = $this->teacher->getClasses($teacherId);

        view('teacher.dashboard', [
            'teacher' => $teacher,
            'classes' => $classes
        ]);
    }
}
