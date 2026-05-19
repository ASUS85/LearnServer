<?php
/**
 * =====================================================
 * STUDENT CONTROLLER
 * =====================================================
 * Contrôle le tableau de bord étudiant
 */

class StudentController {

    private $pdo;
    private $student;

    public function __construct($pdo) {
        AuthMiddleware::requireStudent();
        $this->pdo = $pdo;
        $this->student = new Student($pdo);
    }

    /**
     * Dashboard étudiant
     */
    public function dashboard() {
        $studentId = $_SESSION['user_id'];
        $student = $this->student->findById($studentId);
        $notes = $this->student->getNotes($studentId);

        view('student.dashboard', [
            'student' => $student,
            'notes'   => $notes
        ]);
    }
}
