<?php
session_start();

// Verificar si el usuario está autenticado
function estaAutenticado() {
    return isset($_SESSION['usuario_id']);
}

// Redirigir si no está autenticado
function verificarAutenticacion() {
    if (!estaAutenticado()) {
        header('Location: /nexustech/auth/login.php');
        exit;
    }
}

// Obtener usuario actual
function obtenerUsuario($pdo) {
    if (!estaAutenticado()) return null;
    
    $stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE usuario_id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Validar credenciales
function validarCredenciales($pdo, $email, $password) {
    $stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();
    
    if ($usuario && password_verify($password, $usuario['password_hash'])) {
        return $usuario;
    }
    
    return false;
}
?>