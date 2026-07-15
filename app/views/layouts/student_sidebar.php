<?php
$currentPage = basename($_SERVER['REQUEST_URI']);
?>

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
            <li class="menu-item <?php echo (strpos($currentPage, 'dashboard') !== false) ? 'active' : ''; ?>">
                <a href="<?php echo baseUrl('student/dashboard'); ?>">
                    <i data-lucide="layout-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="menu-item <?php echo (strpos($currentPage, 'notes') !== false) ? 'active' : ''; ?>">
                <a href="<?php echo baseUrl('student/notes'); ?>">
                    <i data-lucide="clipboard-list"></i>
                    <span>Mes Notes</span>
                </a>
            </li>
            <li class="menu-item <?php echo (strpos($currentPage, 'schedule') !== false) ? 'active' : ''; ?>">
                <a href="<?php echo baseUrl('student/schedule'); ?>">
                    <i data-lucide="calendar-days"></i>
                    <span>Calendrier</span>
                </a>
            </li>
            <li class="menu-item <?php echo (strpos($currentPage, 'profile') !== false) ? 'active' : ''; ?>">
                <a href="<?php echo baseUrl('student/profile'); ?>">
                    <i data-lucide="user"></i>
                    <span>Profil</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-footer">
        <div class="admin-profile">
            <div class="avatar"><?php echo strtoupper(substr($_SESSION['user_prenom'] ?? $_SESSION['user_nom'] ?? 'S', 0, 1)); ?></div>
            <div>
                <h4><?php echo htmlspecialchars(trim(($_SESSION['user_prenom'] ?? '') . ' ' . ($_SESSION['user_nom'] ?? 'Étudiant'))); ?></h4>
                <span>Étudiant</span>
            </div>
        </div>

        <a href="<?php echo baseUrl('logout'); ?>" class="logout-btn">
            <i data-lucide="log-out"></i>
            Déconnexion
        </a>
    </div>
</aside>
