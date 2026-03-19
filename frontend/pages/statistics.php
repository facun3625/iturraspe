<?php
// Ruta: control_stock/frontend/pages/statistics.php

// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../backend/controllers/authController.php';

$authController = new AuthController();

// Verifica si el usuario está autenticado
if (!$authController->isLoggedIn()) {
    header("Location: login.php"); // Redirige al login si no está autenticado
    exit();
}
require_once '../../backend/controllers/clientController.php';
$clientController = new ClientController();
$clients = $clientController->getClientsWithDebt(); // Este método incluirá el cálculo de deuda

// Calcular el total de deuda
$totalDebt = array_reduce($clients, function ($sum, $client) {
    return $sum + ($client['debt'] > 0 ? $client['debt'] : 0);
}, 0);

if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo "<div class='alert alert-success text-center'>Cliente añadido correctamente.</div>";
}
require_once '../../backend/controllers/productController.php';
$productController = new ProductController();
try {
    $totalStockValue = $productController->getTotalStockValue();
} catch (Exception $e) {
    $totalStockValue = 0;
    $error = "Error al calcular el valor total del stock: " . $e->getMessage();
}
require_once '../../backend/controllers/saleController.php';

$saleController = new SaleController();

// Obtener años y meses con ventas
$years = $saleController->getYearsWithSales();
$months = $saleController->getMonthsWithSales();

// Obtener ventas filtradas por mes y año
$selectedYear = $_GET['year'] ?? null;
$selectedMonth = $_GET['month'] ?? null;

if ($selectedYear) {
    if ($selectedMonth) {
        // Ventas por día del mes seleccionado
        $salesByDay = $saleController->getSalesByYearAndMonth($selectedYear, $selectedMonth);
        $chartLabels = array_keys($salesByDay);
        $chartValues = array_values($salesByDay);
        $chartTitle = "Ventas diarias - $selectedMonth/$selectedYear";
        $totalSales = array_sum($chartValues);
    } else {
        // Ventas por mes del año seleccionado
        $salesByMonth = $saleController->getSalesByYearAndMonth($selectedYear);
        $chartLabels = array_keys($salesByMonth);
        $chartValues = array_values($salesByMonth);
        $chartTitle = "Ventas mensuales - Año $selectedYear";
        $totalSales = array_sum($chartValues);
    }
} else {
    $chartLabels = [];
    $chartValues = [];
    $chartTitle = "Seleccione un año o mes para visualizar las ventas.";
    $totalSales = 0;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <?php include_once '../components/navbar.php'; ?>

    <div class="rounded-container-table">
        <h4 class="text-center">Estadísticas</h4><br>

        <div class="alert alert-info text-left">
            <strong>Total de Deuda:</strong> $<?php echo number_format($totalDebt, 2); ?>
        </div>
        <div class="alert alert-info text-left">
            <strong>Valor total del Stock según Costo:</strong> $<?php echo number_format($totalStockValue, 2); ?>
        </div>

        <!-- Filtros por año y mes -->
        <form method="GET" action="statistics.php" class="mb-4">
            <strong>Ventas:</strong><br><br>
            <div class="form-row">
                <div class="col-md-4">
                    <label for="year">Año</label>
                    <select name="year" id="year" class="form-control">
                        <option value="">Seleccione un año</option>
                        <?php foreach ($years as $year): ?>
                            <option value="<?php echo $year; ?>" <?php echo $selectedYear == $year ? 'selected' : ''; ?>>
                                <?php echo $year; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="month">Mes</label>
                    <select name="month" id="month" class="form-control">
                        <option value="">Todos los meses</option>
                        <?php foreach ($months as $key => $month): ?>
                            <option value="<?php echo $key; ?>" <?php echo $selectedMonth == $key ? 'selected' : ''; ?>>
                                <?php echo $month; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                </div>
            </div>
        </form>

        <!-- Total de ventas en el período filtrado -->
        <?php if ($selectedYear): ?>
            <div class="alert alert-info text-center">
                Total de ventas en el período seleccionado: $<?php echo number_format($totalSales, 2); ?>
            </div>
            <div class="text-center">
                <a href="statistics.php" class="btn btn-secondary">Limpiar Filtro</a>
            </div>
        <?php endif; ?>

        <!-- Gráfica de ventas -->
        <?php if (!empty($chartLabels) && !empty($chartValues)): ?>
            <div class="mt-5">
                <h5 class="text-center"><?php echo $chartTitle; ?></h5>
                <canvas id="salesChart"></canvas>
            </div>
        <?php endif; ?>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <?php if (!empty($chartLabels) && !empty($chartValues)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('salesChart').getContext('2d');
            const chartData = {
                labels: <?php echo json_encode($chartLabels); ?>,
                datasets: [{
                    label: 'Ventas ($)',
                    data: <?php echo json_encode($chartValues); ?>,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            };

            const chartOptions = {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return `$${context.raw.toFixed(2)}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Días o Meses'
                        },
                        beginAtZero: true
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Ventas ($)'
                        },
                        beginAtZero: true
                    }
                }
            };

            new Chart(ctx, {
                type: 'line',
                data: chartData,
                options: chartOptions
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>
