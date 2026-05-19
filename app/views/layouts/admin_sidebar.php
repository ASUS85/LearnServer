<?php
/**
 * =====================================================
 * ADMIN SIDEBAR LAYOUT
 * =====================================================
 * Layout principal pour toutes les pages admin
 */

$currentPage = basename($_SERVER['REQUEST_URI']);
?>

<!-- SIDEBAR -->
<aside class="sidebar">

    <!-- TOP -->
    <div>

        <div class="brand">

            <div class="logo-box">
                🎓
            </div>

            <div>

                <h2 class="logo">
                    EduManage
                </h2>

                <span class="logo-subtitle">
                    Academic Prestige
                </span>

            </div>

        </div>

        <!-- MENU -->

        <ul class="menu">

            <li class="menu-item <?php echo (strpos($currentPage, 'dashboard') !== false) ? 'active' : ''; ?>">

                <a href="<?php echo baseUrl('admin/dashboard'); ?>">

                    <i data-lucide="layout-dashboard"></i>

                    <span>Dashboard</span>

                </a>

            </li>

            <li class="menu-item <?php echo (strpos($currentPage, 'students') !== false) ? 'active' : ''; ?>">

                <a href="<?php echo baseUrl('admin/students'); ?>">

                    <i data-lucide="graduation-cap"></i>

                    <span>Étudiants</span>

                </a>

            </li>

            <li class="menu-item <?php echo (strpos($currentPage, 'teachers') !== false) ? 'active' : ''; ?>">

                <a href="<?php echo baseUrl('admin/teachers'); ?>">

                    <i data-lucide="users"></i>

                    <span>Enseignants</span>

                </a>

            </li>

            <li class="menu-item <?php echo (strpos($currentPage, 'subjects') !== false) ? 'active' : ''; ?>">

                <a href="<?php echo baseUrl('admin/subjects'); ?>">

                    <i data-lucide="book-open"></i>

                    <span>Matières</span>

                </a>

            </li>

            <li class="menu-item <?php echo (strpos($currentPage, 'schedule') !== false) ? 'active' : ''; ?>">

                <a href="<?php echo baseUrl('admin/schedule'); ?>">

                    <i data-lucide="calendar-days"></i>

                    <span>Emploi du Temps</span>

                </a>

            </li>

            <li class="menu-item <?php echo (strpos($currentPage, 'notes') !== false) ? 'active' : ''; ?>">

                <a href="<?php echo baseUrl('admin/notes'); ?>">

                    <i data-lucide="clipboard-list"></i>

                    <span>Notes</span>

                </a>

            </li>

            <li class="menu-item <?php echo (strpos($currentPage, 'settings') !== false) ? 'active' : ''; ?>">

                <a href="<?php echo baseUrl('admin/settings'); ?>">

                    <i data-lucide="settings"></i>

                    <span>Paramètres</span>

                </a>

            </li>

        </ul>

    </div>

    <!-- BOTTOM -->

    <div class="sidebar-footer">

        <div class="admin-profile">

            <div class="avatar">
                <?php echo strtoupper(substr($_SESSION['user_nom'], 0, 1)); ?>
            </div>

            <div>

                <h4>
                    <?php echo htmlspecialchars($_SESSION['user_nom']); ?>
                </h4>

                <span>
                    Administrateur
                </span>

            </div>

        </div>

        <a href="<?php echo baseUrl('logout'); ?>"
           class="logout-btn">

            <i data-lucide="log-out"></i>

            Déconnexion

        </a>

    </div>

</aside>
