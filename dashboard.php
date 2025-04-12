<?php
require_once 'db.php';
require_once 'auth.php';

verificarAutenticacion();

// Obtener estadísticas
$stats = [
    'total_clientes' => $pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn(),
    'clientes_activos' => $pdo->query("SELECT COUNT(*) FROM clientes WHERE activo = 1")->fetchColumn(),
    'clientes_nuevos_mes' => $pdo->query("SELECT COUNT(*) FROM clientes WHERE fecha_registro >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn(),
    'clientes_vip' => $pdo->query("SELECT COUNT(*) FROM clientes WHERE tipo_cliente = 'VIP'")->fetchColumn()
];

// Obtener últimos clientes registrados
$ultimos_clientes = $pdo->query("SELECT * FROM clientes ORDER BY fecha_registro DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

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
                <h1 class="h2"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h1>
            </div>

            <!-- Tarjetas de estadísticas -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card text-white bg-primary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total Clientes</h6>
                                    <h2 class="mb-0"><?= $stats['total_clientes'] ?></h2>
                                </div>
                                <i class="bi bi-people fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-white bg-success h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Clientes Activos</h6>
                                    <h2 class="mb-0"><?= $stats['clientes_activos'] ?></h2>
                                </div>
                                <i class="bi bi-check-circle fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-white bg-info h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Nuevos (30 días)</h6>
                                    <h2 class="mb-0"><?= $stats['clientes_nuevos_mes'] ?></h2>
                                </div>
                                <i class="bi bi-graph-up fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-white bg-warning h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Clientes VIP</h6>
                                    <h2 class="mb-0"><?= $stats['clientes_vip'] ?></h2>
                                </div>
                                <i class="bi bi-star-fill fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos y últimos clientes -->
            <div class="row">
                <div class="col-md-8 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Registro de Clientes (Últimos 6 meses)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="clientesChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="bi bi-people me-2"></i>Últimos Clientes Registrados</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <?php foreach ($ultimos_clientes as $cliente): ?>
                                <div class="list-group-item border-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1"><?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?></h6>
                                            <small class="text-muted"><?= date('d/m/Y', strtotime($cliente['fecha_registro'])) ?></small>
                                        </div>
                                        <span class="badge bg-<?= $cliente['tipo_cliente'] === 'VIP' ? 'warning' : 'primary' ?>">
                                            <?= $cliente['tipo_cliente'] ?>
                                        </span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de clientes por mes
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('clientesChart').getContext('2d');
    
    // Datos de ejemplo (deberías reemplazarlos con datos reales de tu BD)
    const clientesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            datasets: [{
                label: 'Clientes registrados',
                data: [12, 19, 15, 20, 18, 25],
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 5
                    }
                }
            }
        }
    });
});
</script>

<?php include 'footer.php'; ?>