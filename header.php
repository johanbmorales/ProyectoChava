<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Sitio Web</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- En tu header.php o antes del script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <header>
        <!-- Navbar principal -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold" href="index.php">
                    Nexus tech
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarNav" aria-controls="sidebarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 d-lg-none">
                        <li class="nav-item">
                            <a class="nav-link active" href="#"><i class="bi bi-house-door me-1"></i> Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="bi bi-person me-1"></i> Perfil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="bi bi-envelope me-1"></i> Mensajes</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-gear me-1"></i> Configuración
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">Ajustes</a></li>
                                <li><a class="dropdown-item" href="#">Privacidad</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <!-- Sidebar Offcanvas -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarNav" aria-labelledby="sidebarNavLabel">
            <div class="offcanvas-header bg-primary text-white">
                <h5 class="offcanvas-title" id="sidebarNavLabel">Menú Principal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-0">
                <nav class="nav flex-column">
                    <a class="nav-link px-4 py-3 border-bottom" href="#">
                        <i class="bi bi-house-door me-2"></i> Inicio
                    </a>
                    <a class="nav-link px-4 py-3 border-bottom" href="#">
                        <i class="bi bi-person me-2"></i> Perfil
                    </a>
                    <a class="nav-link px-4 py-3 border-bottom" href="#">
                        <i class="bi bi-envelope me-2"></i> Mensajes
                        <span class="badge bg-danger rounded-pill ms-2">3</span>
                    </a>
                    <a class="nav-link px-4 py-3 border-bottom" href="#">
                        <i class="bi bi-gear me-2"></i> Configuración
                    </a>
                    <a class="nav-link px-4 py-3 border-bottom" href="#">
                        <i class="bi bi-info-circle me-2"></i> Acerca de
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="nav-link px-4 py-3 text-danger" href="#">
                        <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>