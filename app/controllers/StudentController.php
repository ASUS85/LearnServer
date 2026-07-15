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
        if (!$student) {
            session_destroy();
            redirect('index.php');
        }

        $notes = $this->student->getNotes($studentId);
        $statistics = $this->student->getStatistics($studentId);
        $today = $this->getTodayLabel();
        $todayCourses = $this->student->getTodaySchedule($studentId, $today);

        view('student.dashboard', [
            'student' => $student,
            'notes'   => $notes,
            'statistics' => $statistics,
            'today' => $today,
            'todayCourses' => $todayCourses
        ]);
    }

    /**
     * Calendrier étudiant
     */
    public function schedule() {
        $studentId = $_SESSION['user_id'];
        $student = $this->student->findById($studentId);

        if (!$student) {
            session_destroy();
            redirect('index.php');
        }

        $weeklySchedule = $this->student->getWeeklySchedule($studentId);
        $calendarDays = $this->groupScheduleByDay($weeklySchedule);
        $today = $this->getTodayLabel();

        view('student.schedule', [
            'student' => $student,
            'weeklySchedule' => $weeklySchedule,
            'calendarDays' => $calendarDays,
            'today' => $today
        ]);
    }

    /**
     * Notes étudiant
     */
    public function notes() {
        $studentId = $_SESSION['user_id'];
        $student = $this->student->findById($studentId);
        if (!$student) {
            session_destroy();
            redirect('index.php');
        }

        $notes = $this->student->getNotes($studentId);

        view('student.notes', [
            'student' => $student,
            'notes' => $notes
        ]);
    }

    /**
     * Profil étudiant
     */
    public function profile() {
        $studentId = $_SESSION['user_id'];
        $student = $this->student->findById($studentId);
        if (!$student) {
            session_destroy();
            redirect('index.php');
        }

        view('student.profile', [
            'student' => $student
        ]);
    }

    private function getTodayLabel() {
        $jours = [
            'Sunday' => 'Dimanche',
            'Monday' => 'Lundi',
            'Tuesday' => 'Mardi',
            'Wednesday' => 'Mercredi',
            'Thursday' => 'Jeudi',
            'Friday' => 'Vendredi',
            'Saturday' => 'Samedi'
        ];

        return $jours[date('l')] ?? 'Lundi';
    }

    private function groupScheduleByDay(array $weeklySchedule) {
        $orderedDays = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $grouped = array_fill_keys($orderedDays, []);

        foreach ($weeklySchedule as $item) {
            $day = $item['jour'] ?? 'Lundi';
            if (!isset($grouped[$day])) {
                $grouped[$day] = [];
            }
            $grouped[$day][] = $item;
        }

        return $grouped;
    }
}
