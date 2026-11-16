<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) Recoger credenciales
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // 2) Validar campos vacíos
    if ($username === '' || $password === '') {
        header('Location: login.php?error=empty');
        exit;
    }

    // 3) Cargar usuarios desde JSON
    $usersFile = __DIR__ . '/../usuarios.json';
    if (!is_readable($usersFile)) {
        header('Location: login.php?error=file');
        exit;
    }
    $users = json_decode(file_get_contents($usersFile), true);
    if (!is_array($users)) {
        header('Location: login.php?error=file');
        exit;
    }

    // 4) Buscar usuario y verificar contraseña
    foreach ($users as $user) {
        if ($user['username'] === $username && password_verify($password, $user['password'])) {
            // Login exitoso
            $_SESSION['user_id']  = $username;                   // No hay ID numérico en el JSON
            $_SESSION['username'] = $username;
            $_SESSION['role']     = $user['role'] ?? 'admin';    // Puedes agregar "role" al JSON si lo necesitas
            header('Location: index.php');
            exit;
        }
    }

    // 5) Si no coincide, error
    header('Location: login.php?error=invalid');
    exit;
}

// Si alguien accede sin POST, lo enviamos al login
header('Location: login.php');
exit;
