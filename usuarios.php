<?php
require_once 'db.php';
require_once 'auth.php';

verificarAutenticacion();

// Verificar si el usuario tiene permisos de administrador


// Procesar acciones CRUD a través de AJAX
if (isset($_POST['action'])) {
    $response = [];
    
    // Crear nuevo usuario
    if ($_POST['action'] == 'create') {
        try {
            // Validar que las contraseñas coincidan
            if ($_POST['password'] !== $_POST['confirm_password']) {
                throw new Exception("Las contraseñas no coinciden");
            }
            
            // Hash de la contraseña
            $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO usuarios (username, email, password_hash, nombre, apellido, rol, activo) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $_POST['username'],
                $_POST['email'],
                $password_hash,
                $_POST['nombre'],
                $_POST['apellido'],
                $_POST['rol'],
                isset($_POST['activo']) ? 1 : 0
            ]);
            
            $response['status'] = 'success';
            $response['message'] = 'Usuario creado correctamente';
        } catch (PDOException $e) {
            $response['status'] = 'error';
            $response['message'] = 'Error al crear usuario: ' . $e->getMessage();
        } catch (Exception $e) {
            $response['status'] = 'error';
            $response['message'] = $e->getMessage();
        }
        
        echo json_encode($response);
        exit;
    }
    
    // Actualizar usuario existente
    if ($_POST['action'] == 'update') {
        try {
            // Construir la consulta dinámicamente para manejar la actualización opcional de contraseña
            $sql = "UPDATE usuarios SET 
                    username = ?, 
                    email = ?, 
                    nombre = ?, 
                    apellido = ?, 
                    rol = ?, 
                    activo = ?";
            
            $params = [
                $_POST['username'],
                $_POST['email'],
                $_POST['nombre'],
                $_POST['apellido'],
                $_POST['rol'],
                isset($_POST['activo']) ? 1 : 0,
                $_POST['usuario_id']
            ];
            
            // Si se proporcionó una nueva contraseña
            if (!empty($_POST['password'])) {
                if ($_POST['password'] !== $_POST['confirm_password']) {
                    throw new Exception("Las contraseñas no coinciden");
                }
                
                $sql .= ", password_hash = ?";
                $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                array_splice($params, count($params)-1, 0, $password_hash);
            }
            
            $sql .= " WHERE usuario_id = ?";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            $response['status'] = 'success';
            $response['message'] = 'Usuario actualizado correctamente';
        } catch (PDOException $e) {
            $response['status'] = 'error';
            $response['message'] = 'Error al actualizar usuario: ' . $e->getMessage();
        } catch (Exception $e) {
            $response['status'] = 'error';
            $response['message'] = $e->getMessage();
        }
        
        echo json_encode($response);
        exit;
    }
    
    // Eliminar usuario
    if ($_POST['action'] == 'delete') {
        try {
            // No permitir eliminar el propio usuario
            if ($_POST['usuario_id'] == $_SESSION['usuario_id']) {
                throw new Exception("No puedes eliminar tu propio usuario");
            }
            
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE usuario_id = ?");
            $stmt->execute([$_POST['usuario_id']]);
            
            $response['status'] = 'success';
            $response['message'] = 'Usuario eliminado correctamente';
        } catch (PDOException $e) {
            $response['status'] = 'error';
            $response['message'] = 'Error al eliminar usuario: ' . $e->getMessage();
        } catch (Exception $e) {
            $response['status'] = 'error';
            $response['message'] = $e->getMessage();
        }
        
        echo json_encode($response);
        exit;
    }
    
    // Obtener datos de un usuario para editar
    if ($_POST['action'] == 'get') {
        try {
            $stmt = $pdo->prepare("SELECT usuario_id, username, email, nombre, apellido, rol, activo FROM usuarios WHERE usuario_id = ?");
            $stmt->execute([$_POST['usuario_id']]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario) {
                $response = $usuario;
                $response['status'] = 'success';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Usuario no encontrado';
            }
        } catch (PDOException $e) {
            $response['status'] = 'error';
            $response['message'] = 'Error al obtener datos del usuario: ' . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Obtener todos los usuarios para la tabla
$busqueda = isset($_GET['search']) ? $_GET['search'] : '';
$pagina = isset($_GET['page']) ? $_GET['page'] : 1;
$por_pagina = 10;
$offset = ($pagina - 1) * $por_pagina;

$where = '';
if (!empty($busqueda)) {
    $where = " WHERE nombre LIKE :busqueda OR apellido LIKE :busqueda OR email LIKE :busqueda OR username LIKE :busqueda";
}

// Consulta para contar el total de registros
$stmt_count = $pdo->prepare("SELECT COUNT(*) FROM usuarios" . $where);
if (!empty($busqueda)) {
    $busqueda_param = '%' . $busqueda . '%';
    $stmt_count->bindParam(':busqueda', $busqueda_param);
}
$stmt_count->execute();
$total_registros = $stmt_count->fetchColumn();
$total_paginas = ceil($total_registros / $por_pagina);

// Consulta para obtener los usuarios con paginación
$query = "SELECT usuario_id, username, email, nombre, apellido, rol, activo, fecha_creacion, ultimo_login 
          FROM usuarios" . $where . " ORDER BY fecha_creacion DESC LIMIT :offset, :por_pagina";
$stmt = $pdo->prepare($query);
if (!empty($busqueda)) {
    $busqueda_param = '%' . $busqueda . '%';
    $stmt->bindParam(':busqueda', $busqueda_param);
}
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':por_pagina', $por_pagina, PDO::PARAM_INT);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="clientes.php">
                            <i class="bi bi-people me-2"></i> Clientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="usuarios.php">
                            <i class="bi bi-person-gear me-2"></i> Usuarios
                        </a>
                    </li>
                    <li class="nav-item mt-3">
                        <a class="nav-link text-danger" href="logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Contenido principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="bi bi-person-gear me-2"></i>Gestión de Usuarios</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario">
                        <i class="bi bi-plus-circle me-1"></i> Nuevo Usuario
                    </button>
                </div>
            </div>

            <!-- Barra de búsqueda -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <form action="" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" placeholder="Buscar usuario..." value="<?= htmlspecialchars($busqueda) ?>">
                        <button class="btn btn-outline-primary" type="submit">Buscar</button>
                    </form>
                </div>
            </div>

            <!-- Tabla de usuarios -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Registro</th>
                                    <th>Último Login</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($usuarios) > 0): ?>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <tr>
                                            <td><?= $usuario['usuario_id'] ?></td>
                                            <td><?= htmlspecialchars($usuario['username']) ?></td>
                                            <td><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></td>
                                            <td><?= htmlspecialchars($usuario['email']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= 
                                                    $usuario['rol'] === 'Administrador' ? 'danger' : 
                                                    ($usuario['rol'] === 'Gestor' ? 'info' : 'secondary') 
                                                ?>">
                                                    <?= $usuario['rol'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $usuario['activo'] ? 'success' : 'danger' ?>">
                                                    <?= $usuario['activo'] ? 'Activo' : 'Inactivo' ?>
                                                </span>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($usuario['fecha_creacion'])) ?></td>
                                            <td><?= $usuario['ultimo_login'] ? date('d/m/Y H:i', strtotime($usuario['ultimo_login'])) : 'Nunca' ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-warning btn-sm btn-editar" data-id="<?= $usuario['usuario_id'] ?>" title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-eliminar" data-id="<?= $usuario['usuario_id'] ?>" title="Eliminar" <?= $usuario['usuario_id'] == $_SESSION['usuario_id'] ? 'disabled' : '' ?>>
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No se encontraron usuarios</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <?php if ($total_paginas > 1): ?>
                        <nav aria-label="Paginación de usuarios">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?= ($pagina <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $pagina - 1 ?><?= !empty($busqueda) ? '&search=' . urlencode($busqueda) : '' ?>" aria-label="Anterior">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                
                                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                    <li class="page-item <?= ($pagina == $i) ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?><?= !empty($busqueda) ? '&search=' . urlencode($busqueda) : '' ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <li class="page-item <?= ($pagina >= $total_paginas) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $pagina + 1 ?><?= !empty($busqueda) ? '&search=' . urlencode($busqueda) : '' ?>" aria-label="Siguiente">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal para Crear/Editar Usuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUsuarioLabel">Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formUsuario">
                    <input type="hidden" id="usuario_id" name="usuario_id">
                    <input type="hidden" id="action" name="action" value="create">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="username" class="form-label">Username *</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label for="apellido" class="form-label">Apellido *</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3" id="password-fields">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Contraseña *</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="col-md-6">
                            <label for="confirm_password" class="form-label">Confirmar Contraseña *</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="rol" class="form-label">Rol *</label>
                            <select class="form-select" id="rol" name="rol" required>
                                <option value="Administrador">Administrador</option>
                                <option value="Gestor">Gestor</option>
                                <option value="Consultor">Consultor</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="activo" name="activo" checked>
                                <label class="form-check-label" for="activo">
                                    Usuario Activo
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <small>
                            <i class="bi bi-info-circle"></i> La contraseña debe tener al menos 8 caracteres.
                            Para actualizar la contraseña de un usuario existente, complete los campos de contraseña.
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardar">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 y jQuery -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Variable para controlar si es edición o creación
    let esEdicion = false;
    let usuarioIdActual = null;

    // Configurar modal para nuevo usuario
    $('[data-bs-target="#modalUsuario"]').click(function() {
        esEdicion = false;
        $('#formUsuario')[0].reset();
        $('#usuario_id').val('');
        $('#action').val('create');
        $('#modalUsuarioLabel').text('Nuevo Usuario');
        $('#password-fields').show();
        $('#password, #confirm_password').prop('required', true);
        $('.alert-info small').html('<i class="bi bi-info-circle"></i> La contraseña debe tener al menos 8 caracteres.');
    });

    // Cargar datos del usuario para editar
    $(document).on('click', '.btn-editar', function() {
        esEdicion = true;
        usuarioIdActual = $(this).data('id');
        
        // Configurar modal para edición
        $('#formUsuario')[0].reset();
        $('#action').val('update');
        $('#modalUsuarioLabel').text('Editar Usuario');
        $('#password-fields').show();
        $('#password, #confirm_password').prop('required', false).val('');
        $('.alert-info small').html('<i class="bi bi-info-circle"></i> Deje los campos de contraseña vacíos si no desea cambiarla.');

        // Obtener datos del usuario
        $.ajax({
            url: 'usuarios.php',
            type: 'POST',
            data: {
                action: 'get',
                usuario_id: usuarioIdActual
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#usuario_id').val(response.usuario_id);
                    $('#username').val(response.username);
                    $('#email').val(response.email);
                    $('#nombre').val(response.nombre);
                    $('#apellido').val(response.apellido);
                    $('#rol').val(response.rol);
                    $('#activo').prop('checked', response.activo == 1);
                    
                    // Mostrar el modal después de cargar los datos
                    $('#modalUsuario').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Error al obtener datos del usuario'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al cargar datos: ' + error
                });
                console.error('Error AJAX:', status, error);
            }
        });
    });

    // Eliminar usuario
    $(document).on('click', '.btn-eliminar', function() {
        const usuarioId = $(this).data('id');
        
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'usuarios.php',
                    type: 'POST',
                    data: {
                        action: 'delete',
                        usuario_id: usuarioId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al eliminar usuario'
                        });
                    }
                });
            }
        });
    });

    // Guardar usuario
    $('#btnGuardar').click(function() {
        const formData = $('#formUsuario').serialize();
        const action = $('#action').val();
        
        // Validaciones
        if (action === 'create' || (action === 'update' && $('#password').val())) {
            if ($('#password').val() !== $('#confirm_password').val()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Las contraseñas no coinciden'
                });
                return;
            }
            
            if ($('#password').val().length < 8) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La contraseña debe tener al menos 8 caracteres'
                });
                return;
            }
        }
        
        $.ajax({
            url: 'usuarios.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        $('#modalUsuario').modal('hide');
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al guardar: ' + error
                });
            }
        });
    });
});
</script>

<?php include 'footer.php'; ?>