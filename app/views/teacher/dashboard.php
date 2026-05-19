<?php
/**
 * Vue Dashboard Enseignant
 * Variables: $teacher, $classes
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Enseignant - EduManage</title>
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
                    <span class="logo-subtitle">Portail Enseignant</span>
                </div>
            </div>

            <ul class="menu">
                <li class="menu-item active">
                    <a href="<?php echo baseUrl('teacher/dashboard'); ?>">
                        <i data-lucide="layout-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="<?php echo baseUrl('teacher/classes'); ?>">
                        <i data-lucide="book-open"></i>
                        <span>Mes Classes</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="<?php echo baseUrl('teacher/schedule'); ?>">
                        <i data-lucide="calendar-days"></i>
                        <span>Mon Emploi du Temps</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="<?php echo baseUrl('teacher/grades'); ?>">
                        <i data-lucide="clipboard-list"></i>
                        <span>Saisir Notes</span>
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
                    <span>Enseignant</span>
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
                    <span class="hero-badge">📚 Portail Enseignant</span>
                    <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['user_prenom']); ?></h1>
                    <p>Gérez vos classes, notes et emploi du temps</p>
                </div>
            </div>
        </section>

        <section class="statistics">
            <div class="stat-card">
                <div class="stat-icon students">
                    <i data-lucide="book-open"></i>
                </div>
                <div class="stat-content">
                    <h3>Mes Classes</h3>
                    <p class="stat-number"><?php echo count($classes ?? []); ?></p>
                    <span class="stat-label">Classes enseignées</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon teachers">
                    <i data-lucide="graduation-cap"></i>
                </div>
                <div class="stat-content">
                    <h3>Statut</h3>
                    <p class="stat-number" style="font-size: 1em;">Actif</p>
                    <span class="stat-label">Compte enseignant actif</span>
                </div>
            </div>
        </section>

        <section class="quick-actions">
            <h2>Actions Rapides</h2>
            <div class="actions-grid">
                <a href="<?php echo baseUrl('teacher/classes'); ?>" class="action-btn">
                    <i data-lucide="eye"></i>
                    <span>Voir Mes Classes</span>
                </a>
                <a href="<?php echo baseUrl('teacher/grades'); ?>" class="action-btn">
                    <i data-lucide="edit"></i>
                    <span>Saisir Notes</span>
                </a>
                <a href="<?php echo baseUrl('teacher/schedule'); ?>" class="action-btn">
                    <i data-lucide="calendar"></i>
                    <span>Voir Horaires</span>
                </a>
                <a href="<?php echo baseUrl('teacher/profile'); ?>" class="action-btn">
                    <i data-lucide="user"></i>
                    <span>Mon Profil</span>
                </a>
            </div>
        </section>

        <?php if (!empty($classes)): ?>
            <section class="section-content">
                <h2>Mes Classes</h2>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Matière</th>
                                <th>Classe</th>
                                <th>Crédits</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($classes as $class): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($class['nom_matiere'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($class['nom_classe'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($class['credit'] ?? '-'); ?></td>
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
