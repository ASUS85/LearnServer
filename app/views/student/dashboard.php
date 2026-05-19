<?php
/**
 * Vue Dashboard Étudiant
 * Variables: $student, $notes
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Étudiant - EduManage</title>
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

<div class="container">
    <aside class="sidebar">
        <div>
            <div class="brand">
                <div class="logo-box">🎓</div>
                <div>
                    <h2 class="logo">EduManage</h2>
                    <span class="logo-subtitle">Portail Étudiant</span>
                </div>
            </div>

            <ul class="menu">
                <li class="menu-item active">
                    <a href="<?php echo baseUrl('student/dashboard'); ?>">
                        <i data-lucide="layout-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="<?php echo baseUrl('student/notes'); ?>">
                        <i data-lucide="clipboard-list"></i>
                        <span>Mes Notes</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="<?php echo baseUrl('student/schedule'); ?>">
                        <i data-lucide="calendar-days"></i>
                        <span>Emploi du Temps</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-footer">
            <div class="admin-profile">
                <div class="avatar">
                    <?php echo strtoupper(substr($_SESSION['user_nom'], 0, 1)); ?>
                </div>
                <div>
                    <h4><?php echo htmlspecialchars($_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom']); ?></h4>
                    <span>Étudiant</span>
                </div>
            </div>
            <a href="<?php echo baseUrl('logout'); ?>" class="logout-btn">
                <i data-lucide="log-out"></i>
                Déconnexion
            </a>
        </div>
    </aside>

    <main class="main-content">
        <section class="hero">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <div class="hero-text">
                    <span class="hero-badge">📚 Portail Académique</span>
                    <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['user_prenom']); ?></h1>
                    <p>Consultez vos notes, emploi du temps et informations académiques</p>
                </div>
            </div>
        </section>

        <section class="statistics">
            <div class="stat-card">
                <div class="stat-icon students">
                    <i data-lucide="clipboard-list"></i>
                </div>
                <div class="stat-content">
                    <h3>Vos Notes</h3>
                    <p class="stat-number"><?php echo count($notes ?? []); ?></p>
                    <span class="stat-label">Évaluations enregistrées</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon teachers">
                    <i data-lucide="users"></i>
                </div>
                <div class="stat-content">
                    <h3>Filière</h3>
                    <p class="stat-number" style="font-size: 1em;">
                        <?php echo htmlspecialchars($student['nom_filiere'] ?? 'Non définie'); ?>
                    </p>
                    <span class="stat-label">Filière actuelle</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon subjects">
                    <i data-lucide="book-open"></i>
                </div>
                <div class="stat-content">
                    <h3>Niveau</h3>
                    <p class="stat-number" style="font-size: 1em;">
                        <?php echo htmlspecialchars($student['nom_niveau'] ?? 'Non défini'); ?>
                    </p>
                    <span class="stat-label">Niveau d'études</span>
                </div>
            </div>
        </section>

        <section class="quick-actions">
            <h2>Actions Rapides</h2>
            <div class="actions-grid">
                <a href="<?php echo baseUrl('student/notes'); ?>" class="action-btn">
                    <i data-lucide="eye"></i>
                    <span>Consulter Notes</span>
                </a>
                <a href="<?php echo baseUrl('student/schedule'); ?>" class="action-btn">
                    <i data-lucide="calendar"></i>
                    <span>Voir Horaires</span>
                </a>
                <a href="<?php echo baseUrl('student/profile'); ?>" class="action-btn">
                    <i data-lucide="user"></i>
                    <span>Mon Profil</span>
                </a>
            </div>
        </section>

        <?php if (!empty($notes)): ?>
            <section class="section-content">
                <h2>Dernières Notes</h2>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Matière</th>
                                <th>Note</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach(array_slice($notes, 0, 5) as $note): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($note['nom_matiere'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($note['note'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($note['date_creation'] ?? '-'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif; ?>
    </main>
</div>

<script>
    lucide.createIcons();
</script>
</body>
</html>
