<?php
// auth/login.php
require_once 'db.php';

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
        error_log("Error de autenticación: " . $e->getMessage());
        return false;
    }
}


// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validación básica
    if (empty($email) || empty($password)) {
        $error = "Por favor ingrese email y contraseña";
    } else {
        $usuario = validarCredenciales($pdo, $email, $password);
        
        if ($usuario) {
            // Regenerar ID de sesión para prevenir fixation
            session_regenerate_id(true);
            
            $_SESSION['usuario_id'] = $usuario['usuario_id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_rol'] = $usuario['rol'];
            
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Credenciales incorrectas";
        }
    }
}

include "header.php";
?>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-8 col-lg-6 col-xl-5">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión</h4>
                        <img src="assets/images/logoWhite.svg" alt="NexusTech Logo" height="30">
                    </div>
                </div>
                <div class="card-body p-4 p-md-5">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold">Correo Electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                <input type="email" class="form-control form-control-lg" id="email" name="email" 
                                       placeholder="usuario@dominio.com" required>
                            </div>
                            <div class="invalid-feedback">
                                Por favor ingrese un email válido
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control form-control-lg" id="password" 
                                       name="password" placeholder="••••••••" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">
                                Por favor ingrese su contraseña
                            </div>
                        </div>

                        
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar
                        </button>
                        

                    </form>
                </div>
            </div>
            
            <div class="text-center mt-4 text-muted">
                <small>&copy; <?= date('Y') ?> NexusTech. Todos los derechos reservados.</small>
            </div>
        </div>
    </div>
</div>

<script>
// Validación del formulario
(function () {
    'use strict'
    
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation')
    
    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                
                form.classList.add('was-validated')
            }, false)
        })
        
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(function(button) {
        button.addEventListener('click', function() {
            const passwordInput = this.previousElementSibling;
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('bi-eye-fill');
            this.querySelector('i').classList.toggle('bi-eye-slash-fill');
        });
    });
})();
</script>

<?php include "footer.php"; ?>