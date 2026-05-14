<?php
session_start();
require_once '../config/database.php';

$email = isset($_POST['email']) ? $_POST['email'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$role = isset($_POST['role']) ? $_POST['role'] : '';

if(empty($email) || empty($password) || empty($role)){
    die("Tous les champs sont obligatoires");
}

$tableMap = array(
    'admin' => 'admins',
    'student' => 'etudiants',
    'teacher' => 'enseignants'
);

$redirectMap = array(
    'admin' => '../admin/dashboard.php',
    'student' => '../student/dashboard.php',
    'teacher' => '../teacher/dashboard.php'
);

$table = $tableMap[$role];

$stmt = $pdo->prepare("SELECT * FROM $table WHERE email = ?");
$stmt->execute(array($email));

$user = $stmt->fetch();

if(!$user){
    die("Compte introuvable");
}

if($password != $user['mot_de_passe']){
    die("Mot de passe incorrect");
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['user_nom'] = $user['nom'];
$_SESSION['user_role'] = $role;

header("Location: " . $redirectMap[$role]);
exit;