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
        if (!$teacher) {
            session_destroy();
            redirect('index.php');
        }

        $statistics = $this->teacher->getStatistics($teacherId);
        $today = $this->getTodayLabel();
        $todayCourses = $this->teacher->getTodaySchedule($teacherId, $today);
        $recentNotes = $this->teacher->getRecentNotes($teacherId, 5);

        view('teacher.dashboard', [
            'teacher' => $teacher,
            'statistics' => $statistics,
            'today' => $today,
            'todayCourses' => $todayCourses,
            'recentNotes' => $recentNotes
        ]);
    }

    /**
     * Calendrier de l'emploi du temps
     */
    public function schedule() {
        $teacherId = $_SESSION['user_id'];
        $teacher = $this->teacher->findById($teacherId);

        if (!$teacher) {
            session_destroy();
            redirect('index.php');
        }

        $weeklySchedule = $this->teacher->getWeeklySchedule($teacherId);
        $calendarDays = $this->groupScheduleByDay($weeklySchedule);
        $today = $this->getTodayLabel();

        view('teacher.schedule', [
            'teacher' => $teacher,
            'weeklySchedule' => $weeklySchedule,
            'calendarDays' => $calendarDays,
            'today' => $today
        ]);
    }

    /**
     * Profil enseignant
     */
    public function profile() {
        $teacherId = $_SESSION['user_id'];
        $teacher = $this->teacher->findById($teacherId);

        if (!$teacher) {
            session_destroy();
            redirect('index.php');
        }

        view('teacher.profile', [
            'teacher' => $teacher
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
