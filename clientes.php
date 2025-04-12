<?php
require_once 'db.php';
require_once 'auth.php';

verificarAutenticacion();

// Procesar acciones CRUD a través de AJAX
if (isset($_POST['action'])) {
    $response = [];
    
    // Crear nuevo cliente
    if ($_POST['action'] == 'create') {
        try {
            $stmt = $pdo->prepare("INSERT INTO clientes (nombre, apellido, email, telefono, direccion, 
                                   fecha_nacimiento, genero, puntos_fidelidad, activo, tipo_cliente) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $_POST['nombre'],
                $_POST['apellido'],
                $_POST['email'],
                $_POST['telefono'],
                $_POST['direccion'],
                $_POST['fecha_nacimiento'],
                $_POST['genero'],
                $_POST['puntos_fidelidad'],
                isset($_POST['activo']) ? 1 : 0,
                $_POST['tipo_cliente']
            ]);
            
            $response['status'] = 'success';
            $response['message'] = 'Cliente agregado correctamente';
        } catch (PDOException $e) {
            $response['status'] = 'error';
            $response['message'] = 'Error al agregar cliente: ' . $e->getMessage();
        }
        
        echo json_encode($response);
        exit;
    }
    
    // Actualizar cliente existente
    if ($_POST['action'] == 'update') {
        try {
            $stmt = $pdo->prepare("UPDATE clientes SET 
                                  nombre = ?, 
                                  apellido = ?, 
                                  email = ?, 
                                  telefono = ?, 
                                  direccion = ?, 
                                  fecha_nacimiento = ?, 
                                  genero = ?, 
                                  puntos_fidelidad = ?, 
                                  activo = ?, 
                                  tipo_cliente = ? 
                                  WHERE cliente_id = ?");
            
            $stmt->execute([
                $_POST['nombre'],
                $_POST['apellido'],
                $_POST['email'],
                $_POST['telefono'],
                $_POST['direccion'],
                $_POST['fecha_nacimiento'],
                $_POST['genero'],
                $_POST['puntos_fidelidad'],
                isset($_POST['activo']) ? 1 : 0,
                $_POST['tipo_cliente'],
                $_POST['cliente_id']
            ]);
            
            $response['status'] = 'success';
            $response['message'] = 'Cliente actualizado correctamente';
        } catch (PDOException $e) {
            $response['status'] = 'error';
            $response['message'] = 'Error al actualizar cliente: ' . $e->getMessage();
        }
        
        echo json_encode($response);
        exit;
    }
    
    // Eliminar cliente
    if ($_POST['action'] == 'delete') {
        try {
            $stmt = $pdo->prepare("DELETE FROM clientes WHERE cliente_id = ?");
            $stmt->execute([$_POST['cliente_id']]);
            
            $response['status'] = 'success';
            $response['message'] = 'Cliente eliminado correctamente';
        } catch (PDOException $e) {
            $response['status'] = 'error';
            $response['message'] = 'Error al eliminar cliente: ' . $e->getMessage();
        }
        
        echo json_encode($response);
        exit;
    }
    
    // Obtener datos de un cliente para editar
    if ($_POST['action'] == 'get') {
        try {
            $stmt = $pdo->prepare("SELECT * FROM clientes WHERE cliente_id = ?");
            $stmt->execute([$_POST['cliente_id']]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($cliente) {
                $response = $cliente;
                $response['status'] = 'success';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Cliente no encontrado';
            }
        } catch (PDOException $e) {
            $response['status'] = 'error';
            $response['message'] = 'Error al obtener datos del cliente: ' . $e->getMessage();
        }
        
        echo json_encode($response);
        exit;
    }
}

// Obtener todos los clientes para la tabla
$busqueda = isset($_GET['search']) ? $_GET['search'] : '';
$pagina = isset($_GET['page']) ? $_GET['page'] : 1;
$por_pagina = 10;
$offset = ($pagina - 1) * $por_pagina;

$where = '';
if (!empty($busqueda)) {
    $where = " WHERE nombre LIKE :busqueda OR apellido LIKE :busqueda OR email LIKE :busqueda";
}

// Consulta para contar el total de registros
$stmt_count = $pdo->prepare("SELECT COUNT(*) FROM clientes" . $where);
if (!empty($busqueda)) {
    $busqueda_param = '%' . $busqueda . '%';
    $stmt_count->bindParam(':busqueda', $busqueda_param);
}
$stmt_count->execute();
$total_registros = $stmt_count->fetchColumn();
$total_paginas = ceil($total_registros / $por_pagina);

// Consulta para obtener los clientes con paginación
$query = "SELECT * FROM clientes" . $where . " ORDER BY fecha_registro DESC LIMIT :offset, :por_pagina";
$stmt = $pdo->prepare($query);
if (!empty($busqueda)) {
    $busqueda_param = '%' . $busqueda . '%';
    $stmt->bindParam(':busqueda', $busqueda_param);
}
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':por_pagina', $por_pagina, PDO::PARAM_INT);
$stmt->execute();
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="clientes.php">
                            <i class="bi bi-people me-2"></i> Clientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="usuarios.php">
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
                <h1 class="h2"><i class="bi bi-people me-2"></i>Gestión de Clientes</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCliente">
                        <i class="bi bi-plus-circle me-1"></i> Nuevo Cliente
                    </button>
                </div>
            </div>

            <!-- Barra de búsqueda -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <form action="" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" placeholder="Buscar cliente..." value="<?= htmlspecialchars($busqueda) ?>">
                        <button class="btn btn-outline-primary" type="submit">Buscar</button>
                    </form>
                </div>
            </div>

            <!-- Tabla de clientes -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($clientes) > 0): ?>
                                    <?php foreach ($clientes as $cliente): ?>
                                        <tr>
                                            <td><?= $cliente['cliente_id'] ?></td>
                                            <td><?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?></td>
                                            <td><?= htmlspecialchars($cliente['email']) ?></td>
                                            <td><?= htmlspecialchars($cliente['telefono']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $cliente['tipo_cliente'] === 'VIP' ? 'warning text-dark' : 'primary' ?>">
                                                    <?= $cliente['tipo_cliente'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $cliente['activo'] ? 'success' : 'danger' ?>">
                                                    <?= $cliente['activo'] ? 'Activo' : 'Inactivo' ?>
                                                </span>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($cliente['fecha_registro'])) ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-info btn-sm btn-ver" data-id="<?= $cliente['cliente_id'] ?>" title="Ver detalles">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-warning btn-sm btn-editar" data-id="<?= $cliente['cliente_id'] ?>" title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-eliminar" data-id="<?= $cliente['cliente_id'] ?>" title="Eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No se encontraron clientes</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <?php if ($total_paginas > 1): ?>
                        <nav aria-label="Paginación de clientes">
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

<!-- Modal para Crear/Editar Cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1" aria-labelledby="modalClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalClienteLabel">Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formCliente">
                    <input type="hidden" id="cliente_id" name="cliente_id">
                    <input type="hidden" id="action" name="action" value="create">
                    
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
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="col-md-6">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección</label>
                        <textarea class="form-control" id="direccion" name="direccion" rows="2"></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento">
                        </div>
                        <div class="col-md-6">
                            <label for="genero" class="form-label">Género</label>
                            <select class="form-select" id="genero" name="genero">
                                <option value="">Seleccionar</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="puntos_fidelidad" class="form-label">Puntos de Fidelidad</label>
                            <input type="number" class="form-control" id="puntos_fidelidad" name="puntos_fidelidad" value="0" min="0">
                        </div>
                        <div class="col-md-6">
                            <label for="tipo_cliente" class="form-label">Tipo de Cliente</label>
                            <select class="form-select" id="tipo_cliente" name="tipo_cliente" required>
                                <option value="NORMAL">NORMAL</option>
                                <option value="VIP">VIP</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="activo" name="activo" checked>
                        <label class="form-check-label" for="activo">Cliente Activo</label>
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

<!-- Modal para Ver Detalles del Cliente -->
<div class="modal fade" id="modalVerCliente" tabindex="-1" aria-labelledby="modalVerClienteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalVerClienteLabel">Detalles del Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card border-0">
                    <div class="card-body p-0">
                        <h4 id="detalle-nombre-completo" class="mb-3"></h4>
                        
                        <table class="table table-borderless">
                            <tr>
                                <th class="text-muted">Email:</th>
                                <td id="detalle-email"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Teléfono:</th>
                                <td id="detalle-telefono"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Dirección:</th>
                                <td id="detalle-direccion"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Fecha de Nacimiento:</th>
                                <td id="detalle-fecha-nacimiento"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Género:</th>
                                <td id="detalle-genero"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Tipo de Cliente:</th>
                                <td id="detalle-tipo-cliente"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Estado:</th>
                                <td id="detalle-activo"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Puntos de Fidelidad:</th>
                                <td id="detalle-puntos"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Fecha de Registro:</th>
                                <td id="detalle-fecha-registro"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 y jQuery -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Limpiar el formulario cuando se muestra el modal de nuevo cliente
    $('#modalCliente').on('show.bs.modal', function(e) {
        if (!$(e.relatedTarget).hasClass('btn-editar')) {
            $('#formCliente')[0].reset();
            $('#cliente_id').val('');
            $('#action').val('create');
            $('#modalClienteLabel').text('Nuevo Cliente');
        }
    });
    
    // Cargar datos del cliente para editar
    $('.btn-editar').click(function() {
        const clienteId = $(this).data('id');
        $('#action').val('update');
        $('#modalClienteLabel').text('Editar Cliente');
        
        // Petición AJAX para obtener los datos del cliente
        $.ajax({
            url: 'clientes.php',
            type: 'POST',
            data: {
                action: 'get',
                cliente_id: clienteId
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#cliente_id').val(response.cliente_id);
                    $('#nombre').val(response.nombre);
                    $('#apellido').val(response.apellido);
                    $('#email').val(response.email);
                    $('#telefono').val(response.telefono);
                    $('#direccion').val(response.direccion);
                    $('#fecha_nacimiento').val(response.fecha_nacimiento);
                    $('#genero').val(response.genero);
                    $('#puntos_fidelidad').val(response.puntos_fidelidad);
                    $('#tipo_cliente').val(response.tipo_cliente);
                    $('#activo').prop('checked', response.activo == 1);
                    
                    $('#modalCliente').modal('show');
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
                    text: 'Ocurrió un error al obtener los datos del cliente'
                });
            }
        });
    });
    
    // Ver detalles del cliente
    $('.btn-ver').click(function() {
        const clienteId = $(this).data('id');
        
        // Petición AJAX para obtener los datos del cliente
        $.ajax({
            url: 'clientes.php',
            type: 'POST',
            data: {
                action: 'get',
                cliente_id: clienteId
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Llenar los detalles en el modal
                    $('#detalle-nombre-completo').text(response.nombre + ' ' + response.apellido);
                    $('#detalle-email').text(response.email || 'No especificado');
                    $('#detalle-telefono').text(response.telefono || 'No especificado');
                    $('#detalle-direccion').text(response.direccion || 'No especificada');
                    
                    // Formatear fecha de nacimiento
                    const fechaNac = response.fecha_nacimiento ? new Date(response.fecha_nacimiento) : null;
                    $('#detalle-fecha-nacimiento').text(fechaNac ? fechaNac.toLocaleDateString() : 'No especificada');
                    
                    // Manejar género
                    let genero = 'No especificado';
                    if (response.genero === 'M') genero = 'Masculino';
                    if (response.genero === 'F') genero = 'Femenino';
                    if (response.genero === 'Otro') genero = 'Otro';
                    $('#detalle-genero').text(genero);
                    
                    // Manejar tipo de cliente con badge
                    const tipoBadge = response.tipo_cliente === 'VIP' ? 
                                      '<span class="badge bg-warning text-dark">VIP</span>' : 
                                      '<span class="badge bg-primary">NORMAL</span>';
                    $('#detalle-tipo-cliente').html(tipoBadge);
                    
                    // Manejar estado con badge
                    const activoBadge = response.activo == 1 ? 
                                      '<span class="badge bg-success">Activo</span>' : 
                                      '<span class="badge bg-danger">Inactivo</span>';
                    $('#detalle-activo').html(activoBadge);
                    
                    $('#detalle-puntos').text(response.puntos_fidelidad || '0');
                    
                    // Formatear fecha de registro
                    const fechaReg = new Date(response.fecha_registro);
                    $('#detalle-fecha-registro').text(fechaReg.toLocaleDateString() + ' ' + fechaReg.toLocaleTimeString());
                    
                    $('#modalVerCliente').modal('show');
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
                    text: 'Ocurrió un error al obtener los datos del cliente'
                });
            }
        });
    });
    
    // Eliminar cliente
    $('.btn-eliminar').click(function() {
        const clienteId = $(this).data('id');
        
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
                // Petición AJAX para eliminar el cliente
                $.ajax({
                    url: 'clientes.php',
                    type: 'POST',
                    data: {
                        action: 'delete',
                        cliente_id: clienteId
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
                            text: 'Ocurrió un error al eliminar el cliente'
                        });
                    }
                });
            }
        });
    });
    
    // Guardar cliente (crear o actualizar)
    $('#btnGuardar').click(function() {
        const formData = $('#formCliente').serialize();
        const action = $('#action').val();
        
        $.ajax({
            url: 'clientes.php',
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
                        $('#modalCliente').modal('hide');
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
                    text: 'Ocurrió un error al guardar los datos'
                });
            }
        });
    });
});
</script>

<?php include 'footer.php'; ?>