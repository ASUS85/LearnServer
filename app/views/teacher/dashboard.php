<?php
/**
 * Vue Dashboard enseignant
 */
$teacherName = trim(($teacher['prenom'] ?? '') . ' ' . ($teacher['nom'] ?? ''));
if ($teacherName === '') {
    $teacherName = 'Enseignant';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Enseignant - LearnServer</title>
    <link rel="stylesheet" href="<?php echo assetUrl('css/dashboardadmin.css'); ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .schedule-list { display: flex; flex-direction: column; gap: 12px; margin-top: 16px; }
        .schedule-item { display: flex; gap: 16px; padding: 14px 16px; border-radius: 14px; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.06); }
        .schedule-time { min-width: 88px; font-weight: 700; color: #93c5fd; }
        .schedule-details h4 { margin: 0 0 4px 0; font-size: 15px; }
        .schedule-details p { margin: 0; color: #cbd5e1; font-size: 13px; }
        .empty-state { margin-top: 16px; color: #94a3b8; }
        .dashboard-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 24px; margin-top: 24px; }
        @media (max-width: 1100px) { .dashboard-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="floating-elements">
    <div class="floating book"></div>
    <div class="floating graduation"></div>
    <div class="floating pencil"></div>
    <div class="floating atom"></div>
    <div class="floating globe"></div>
</div>

<div class="container">
    <?php include basePath('app/views/layouts/teacher_sidebar.php'); ?>

    <main class="main-content">
        <section class="hero">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <div class="hero-text">
                    <span class="hero-badge">Portail Enseignant</span>
                    <h1>Bonjour, <?php echo htmlspecialchars($teacherName); ?> 👋</h1>
                    <p>Vue d'ensemble de vos cours, de vos notes et de votre emploi du temps.</p>
                </div>
                <div class="hero-illustration">
                    <div class="circle circle1"></div>
                    <div class="circle circle2"></div>
                    <div class="circle circle3"></div>
                    <div class="main-icon">📘</div>
                </div>
            </div>
        </section>

        <section class="stats-grid">
            <div class="stat-card">
                <div class="card-glow"></div>
                <div class="stat-icon blue"><i data-lucide="book-open"></i></div>
                <h3>Cours assignés</h3>
                <p><?php echo (int) ($statistics['total_courses'] ?? 0); ?></p>
            </div>

            <div class="stat-card">
                <div class="card-glow"></div>
                <div class="stat-icon purple"><i data-lucide="users"></i></div>
                <h3>Étudiants</h3>
                <p><?php echo (int) ($statistics['total_students'] ?? 0); ?></p>
            </div>

            <div class="stat-card">
                <div class="card-glow"></div>
                <div class="stat-icon cyan"><i data-lucide="clipboard-list"></i></div>
                <h3>Notes publiées</h3>
                <p><?php echo (int) ($statistics['total_notes'] ?? 0); ?></p>
            </div>

            <div class="stat-card">
                <div class="card-glow"></div>
                <div class="stat-icon orange"><i data-lucide="calendar-days"></i></div>
                <h3>Cours aujourd'hui</h3>
                <p><?php echo count($todayCourses ?? []); ?></p>
            </div>
        </section>

        <section class="dashboard-grid">
            <div class="glass-panel schedule-panel">
                <div class="panel-header">
                    <h2>Emploi du temps du jour</h2>
                    <span class="live-badge"><?php echo htmlspecialchars($today ?? ''); ?></span>
                </div>

                <?php if (!empty($todayCourses)): ?>
                    <div class="schedule-list">
                        <?php foreach ($todayCourses as $course): ?>
                            <div class="schedule-item">
                                <div class="schedule-time"><?php echo htmlspecialchars(substr($course['heure_debut'] ?? '', 0, 5)); ?> - <?php echo htmlspecialchars(substr($course['heure_fin'] ?? '', 0, 5)); ?></div>
                                <div class="schedule-details">
                                    <h4><?php echo htmlspecialchars($course['nom_matiere'] ?? 'Matière'); ?></h4>
                                    <p><?php echo htmlspecialchars(($course['nom_filiere'] ?? '') . ' • ' . ($course['nom_niveau'] ?? '')); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="empty-state">Aucun cours prévu aujourd'hui.</p>
                <?php endif; ?>
            </div>

            <div class="glass-panel schedule-panel">
                <div class="panel-header">
                    <h2>Dernières notes</h2>
                    <span class="live-badge">5 dernières</span>
                </div>

                <?php if (!empty($recentNotes)): ?>
                    <div class="schedule-list">
                        <?php foreach ($recentNotes as $note): ?>
                            <div class="schedule-item">
                                <div class="schedule-time"><?php echo htmlspecialchars(substr($note['date_note'] ?? '', 0, 10)); ?></div>
                                <div class="schedule-details">
                                    <h4><?php echo htmlspecialchars(($note['prenom'] ?? '') . ' ' . ($note['nom'] ?? '')); ?></h4>
                                    <p><?php echo htmlspecialchars($note['nom_matiere'] ?? 'Matière'); ?> • Note <?php echo htmlspecialchars($note['note'] ?? ''); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="empty-state">Aucune note récente disponible.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
</div>

<script>
    lucide.createIcons();
</script>
</body>
</html>
