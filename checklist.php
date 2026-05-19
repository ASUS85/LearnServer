<?php
/**
 * =====================================================
 * CHECKLIST DE MIGRATION
 * =====================================================
 * Vérifier que la restructuration MVC est complète
 * 
 * Accédez à: http://localhost/LearnServer/checklist.php
 */

session_start();
$isAuthenticated = isset($_SESSION['user_id']);
$basePath = dirname(__FILE__);

// Vérifier les fichiers essentiels
$requiredFiles = [
    'app/config/database.php',
    'app/helpers/helpers.php',
    'app/middleware/AuthMiddleware.php',
    'app/controllers/AuthController.php',
    'app/controllers/AdminController.php',
    'app/controllers/StudentController.php',
    'app/controllers/TeacherController.php',
    'app/models/User.php',
    'app/models/Admin.php',
    'app/models/Student.php',
    'app/models/Teacher.php',
    'app/views/auth/login.php',
    'app/views/auth/register.php',
    'app/views/admin/dashboard.php',
    'app/views/admin/students.php',
    'app/views/student/dashboard.php',
    'app/views/teacher/dashboard.php',
    'public/index.php',
    '.htaccess',
    'public/.htaccess',
];

$allFound = true;
$fileStatus = [];

foreach ($requiredFiles as $file) {
    $path = $basePath . '/' . $file;
    $exists = file_exists($path);
    $fileStatus[$file] = $exists;
    if (!$exists) {
        $allFound = false;
    }
}

// Vérifier la connexion DB
$dbConnected = false;
$dbError = '';
try {
    require_once 'app/config/database.php';
    if (isset($pdo)) {
        $test = $pdo->query("SELECT 1");
        $dbConnected = true;
    }
} catch (Exception $e) {
    $dbError = $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist Migration MVC - LearnServer</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2em;
            margin-bottom: 10px;
        }
        
        .header p {
            opacity: 0.9;
        }
        
        .content {
            padding: 40px;
        }
        
        .section {
            margin-bottom: 40px;
        }
        
        .section-title {
            font-size: 1.3em;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title .icon {
            font-size: 1.5em;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
            margin-left: 10px;
        }
        
        .status-badge.success {
            background: #d4edda;
            color: #155724;
        }
        
        .status-badge.error {
            background: #f8d7da;
            color: #721c24;
        }
        
        .file-list {
            list-style: none;
            display: grid;
            gap: 8px;
        }
        
        .file-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #ddd;
        }
        
        .file-item.found {
            border-left-color: #28a745;
            background: #f0f8f4;
        }
        
        .file-item.missing {
            border-left-color: #dc3545;
            background: #fdf7f7;
        }
        
        .file-item-icon {
            font-size: 1.2em;
        }
        
        .file-item.found .file-item-icon::before {
            content: '✓';
            color: #28a745;
        }
        
        .file-item.missing .file-item-icon::before {
            content: '✗';
            color: #dc3545;
        }
        
        .file-item-path {
            flex: 1;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }
        
        .summary-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        
        .summary-box.success {
            border-left-color: #28a745;
            background: #f0f8f4;
        }
        
        .summary-box.error {
            border-left-color: #dc3545;
            background: #fdf7f7;
        }
        
        .actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5568d3;
        }
        
        .db-status {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 15px 0;
        }
        
        .db-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .db-item-label {
            color: #666;
            font-size: 0.9em;
            font-weight: 500;
        }
        
        .db-item-value {
            font-family: 'Courier New', monospace;
            color: #333;
        }
        
        .progress {
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            margin: 10px 0;
        }
        
        .progress-bar {
            height: 100%;
            background: #667eea;
            width: 0;
            transition: width 0.3s;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .table tr:hover {
            background: #f8f9fa;
        }
        
        .code {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            overflow-x: auto;
            margin: 10px 0;
        }
        
    </style>
</head>
<body>

<div class="container">

    <div class="header">
        <h1>🚀 Checklist Migration MVC</h1>
        <p>Vérification de la restructuration en architecture MVC</p>
    </div>

    <div class="content">

        <!-- STATUS GLOBAL -->
        <div class="section">
            <h2 class="section-title">
                <span class="icon">📊</span>
                Statut Global
                <span class="status-badge <?php echo $allFound && $dbConnected ? 'success' : 'error'; ?>">
                    <?php echo $allFound && $dbConnected ? '✓ Prêt' : '⚠ À vérifier'; ?>
                </span>
            </h2>

            <?php 
            $found = array_sum(array_map(fn($v) => $v ? 1 : 0, $fileStatus));
            $total = count($fileStatus);
            $percent = round(($found / $total) * 100);
            ?>

            <div class="summary-box <?php echo $percent === 100 ? 'success' : 'error'; ?>">
                <p><strong>Fichiers essentiels:</strong> <?php echo $found; ?>/<?php echo $total; ?> (<?php echo $percent; ?>%)</p>
                <div class="progress">
                    <div class="progress-bar" style="width: <?php echo $percent; ?>%"></div>
                </div>
                <p><strong>Base de données:</strong> <?php echo $dbConnected ? '✓ Connectée' : '✗ Non accessible'; ?></p>
                <?php if (!$dbConnected): ?>
                    <p style="color: #dc3545; margin-top: 5px;"><small><?php echo htmlspecialchars($dbError); ?></small></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- FICHIERS ESSENTIELS -->
        <div class="section">
            <h2 class="section-title">
                <span class="icon">📁</span>
                Fichiers Essentiels
            </h2>

            <ul class="file-list">
                <?php foreach ($fileStatus as $file => $exists): ?>
                    <li class="file-item <?php echo $exists ? 'found' : 'missing'; ?>">
                        <span class="file-item-icon"></span>
                        <span class="file-item-path"><?php echo htmlspecialchars($file); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- BASE DE DONNÉES -->
        <div class="section">
            <h2 class="section-title">
                <span class="icon">🗄️</span>
                Base de Données
            </h2>

            <div class="db-status">
                <div class="db-item">
                    <span class="db-item-label">Statut Connexion</span>
                    <span class="db-item-value"><?php echo $dbConnected ? '✓ Connectée' : '✗ Erreur'; ?></span>
                </div>
                <div class="db-item">
                    <span class="db-item-label">Hôte</span>
                    <span class="db-item-value">localhost</span>
                </div>
                <div class="db-item">
                    <span class="db-item-label">Base de données</span>
                    <span class="db-item-value">gestion_cours_db</span>
                </div>
                <div class="db-item">
                    <span class="db-item-label">Utilisateur</span>
                    <span class="db-item-value">root</span>
                </div>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Table Requise</th>
                        <th>Utilisation</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>admins</code></td>
                        <td>Comptes administrateurs</td>
                    </tr>
                    <tr>
                        <td><code>etudiants</code></td>
                        <td>Profils des étudiants</td>
                    </tr>
                    <tr>
                        <td><code>enseignants</code></td>
                        <td>Profils des enseignants</td>
                    </tr>
                    <tr>
                        <td><code>matieres</code></td>
                        <td>Liste des matières/cours</td>
                    </tr>
                    <tr>
                        <td><code>emplois_temps</code></td>
                        <td>Horaires des cours</td>
                    </tr>
                    <tr>
                        <td><code>notes</code></td>
                        <td>Notes des étudiants</td>
                    </tr>
                    <tr>
                        <td><code>filieres</code></td>
                        <td>Filières d'études</td>
                    </tr>
                    <tr>
                        <td><code>niveaux</code></td>
                        <td>Niveaux d'études</td>
                    </tr>
                    <tr>
                        <td><code>classes</code></td>
                        <td>Classes/Groupes</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ROUTES PRINCIPALES -->
        <div class="section">
            <h2 class="section-title">
                <span class="icon">🛣️</span>
                Routes Principales
            </h2>

            <p style="margin-bottom: 15px; color: #666;">Testez les URLs suivantes:</p>

            <table class="table">
                <thead>
                    <tr>
                        <th>URL</th>
                        <th>Description</th>
                        <th>Auth Requise</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>/LearnServer/</code></td>
                        <td>Page de connexion</td>
                        <td>Non</td>
                    </tr>
                    <tr>
                        <td><code>/LearnServer/register</code></td>
                        <td>Page d'inscription</td>
                        <td>Non</td>
                    </tr>
                    <tr>
                        <td><code>/LearnServer/admin/dashboard</code></td>
                        <td>Dashboard administrateur</td>
                        <td>Oui (admin)</td>
                    </tr>
                    <tr>
                        <td><code>/LearnServer/student/dashboard</code></td>
                        <td>Dashboard étudiant</td>
                        <td>Oui (student)</td>
                    </tr>
                    <tr>
                        <td><code>/LearnServer/teacher/dashboard</code></td>
                        <td>Dashboard enseignant</td>
                        <td>Oui (teacher)</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ACTIONS -->
        <div class="section">
            <h2 class="section-title">
                <span class="icon">⚙️</span>
                Actions
            </h2>

            <div class="actions">
                <a href="/LearnServer/" class="btn btn-primary">Aller à l'application</a>
                <a href="/LearnServer/ARCHITECTURE.md" class="btn btn-primary">Lire la documentation</a>
            </div>
        </div>

        <!-- NOTES -->
        <div class="section">
            <h2 class="section-title">
                <span class="icon">📝</span>
                Notes Important
            </h2>

            <div class="code">
&bull; L'architecture est entièrement basée sur MVC
&bull; Le point d'entrée unique est: public/index.php
&bull; Les assets CSS/JS sont servis depuis: /assets/
&bull; Les sessions utilisateur sont automatiquement gérées
&bull; Les middlewares d'authentification sont en place
&bull; Les vues sont organisées par rôle utilisateur
&bull; Les modèles encapsulent la logique métier
&bull; Les contrôleurs gèrent les requêtes et la logique
            </div>
        </div>

    </div>

</div>

</body>
</html>
