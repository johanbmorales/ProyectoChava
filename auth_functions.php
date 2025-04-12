<?php
// LJ/includes/auth_functions.php

/**
 * Valida las credenciales del usuario
 * @param PDO $pdo Objeto de conexión PDO
 * @param string $email Email del usuario
 * @param string $password Contraseña sin encriptar
 * @return array|false Datos del usuario o false si falla
 */
function validarCredenciales($pdo, $email, $password) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE email = :email AND activo = TRUE LIMIT 1");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario && password_verify($password, $usuario['password_hash'])) {
            return $usuario;
        }
        
        return false;
    } catch (PDOException $e) {
        error_log("Error en validarCredenciales: " . $e->getMessage());
        return false;
    }
}

/**
 * Cierra la sesión y redirige al login
 */
function cerrarSesion() {
    session_start();
    $_SESSION = array();
    session_destroy();
    header('Location: /nexustech/auth/login.php');
    exit;
}