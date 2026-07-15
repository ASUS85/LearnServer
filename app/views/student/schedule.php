<?php
$studentName = trim(($student['prenom'] ?? '') . ' ' . ($student['nom'] ?? ''));
if ($studentName === '') {
    $studentName = 'Étudiant';
}
$calendarStyles = <<<CSS
<style>
.teacher-calendar-grid { display:grid; grid-template-columns:repeat(7,minmax(180px,1fr)); gap:16px; margin-top:24px; overflow-x:auto; }
.teacher-day-column { background:rgba(15,23,42,0.72); border:1px solid rgba(255,255,255,0.08); border-radius:18px; padding:16px; min-height:300px; }
.teacher-day-column.today { border-color:rgba(59,130,246,0.55); box-shadow:0 0 0 1px rgba(59,130,246,0.18) inset; }
.teacher-day-title { display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; }
.teacher-day-title h3 { font-size:16px; margin:0; }
.teacher-day-title span { font-size:12px; color:#94a3b8; }
.teacher-event { background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.06); border-radius:14px; padding:12px; margin-bottom:12px; }
.teacher-event-time { color:#93c5fd; font-weight:700; font-size:13px; margin-bottom:8px; }
.teacher-event h4 { margin:0 0 6px 0; font-size:14px; }
.teacher-event p { margin:0; color:#cbd5e1; font-size:13px; line-height:1.4; }
.teacher-calendar-empty { color:#94a3b8; font-size:13px; }
@media (max-width:1200px) { .teacher-calendar-grid { grid-template-columns:1fr; } }
</style>
CSS;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier - LearnServer</title>
    <link rel="stylesheet" href="<?php echo assetUrl('css/schedule.css'); ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
    <?php echo $calendarStyles; ?>
</head>
<body>
<div class="app-container">
    <?php include basePath('app/views/layouts/student_sidebar.php'); ?>
    <main class="main-content">
        <div class="page-header">
            <div class="header-info">
                <span class="badge-accent">Calendrier étudiant</span>
                <h1>Emploi du temps</h1>
                <p><?php echo htmlspecialchars($studentName); ?>, voici votre semaine.</p>
            </div>
            <div class="header-actions">
                <a href="<?php echo baseUrl('student/dashboard'); ?>" class="btn-primary" style="text-decoration:none;display:inline-flex;align-items:center;gap:8px;">
                    <i data-lucide="layout-dashboard"></i>
                    Retour dashboard
                </a>
            </div>
        </div>

        <section class="glass-panel" style="padding:24px;">
            <div class="panel-header" style="margin-bottom:0;">
                <h2>Calendrier hebdomadaire</h2>
                <span class="live-badge"><?php echo htmlspecialchars($today ?? ''); ?></span>
            </div>

            <div class="teacher-calendar-grid">
                <?php foreach (($calendarDays ?? []) as $dayName => $sessions): ?>
                    <div class="teacher-day-column <?php echo ($dayName === ($today ?? '')) ? 'today' : ''; ?>">
                        <div class="teacher-day-title">
                            <h3><?php echo htmlspecialchars($dayName); ?></h3>
                            <span><?php echo count($sessions); ?> séance(s)</span>
                        </div>
                        <?php if (!empty($sessions)): ?>
                            <?php foreach ($sessions as $session): ?>
                                <div class="teacher-event">
                                    <div class="teacher-event-time"><?php echo htmlspecialchars(substr($session['heure_debut'] ?? '', 0, 5)); ?> - <?php echo htmlspecialchars(substr($session['heure_fin'] ?? '', 0, 5)); ?></div>
                                    <h4><?php echo htmlspecialchars($session['nom_matiere'] ?? 'Matière'); ?></h4>
                                    <p><?php echo htmlspecialchars(($session['nom_filiere'] ?? '') . ' • ' . ($session['nom_niveau'] ?? '')); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="teacher-calendar-empty">Aucune séance prévue.</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
</div>
<script>lucide.createIcons();</script>
</body>
</html>
