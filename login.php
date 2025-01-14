<?php
include 'db.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Buscar en la tabla de admins
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['role'] = 'admin';
        $_SESSION['user_id'] = $admin['id'];
        header("Location: dashboard.html");
        exit;
    }

    // Buscar en la tabla de usuarios
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if ($usuario && password_verify($password, $usuario['password'])) {
        $_SESSION['role'] = 'usuario';
        $_SESSION['user_id'] = $usuario['id'];
        header("Location: dashboard.html");
        exit;
    }

    echo "Usuario o contraseña incorrectos.";
}
?>
