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

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas | Julio Iturraspe</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/modern-system.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .main-layout { display: flex; min-height: 100vh; }
        .content-area { flex: 1; margin-left: 260px; padding: 2rem; background-color: #f8fafc; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: white; border-radius: 1.25rem; padding: 1.5rem; position: relative; overflow: hidden; border: none; box-shadow: var(--shadow-soft); transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon { width: 48px; height: 48px; border-radius: 1rem; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; }
        .stat-value { font-size: 1.75rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem; }
        .stat-label { font-size: 0.875rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.025em; }
        
        .filter-card { background: white; border-radius: 1.25rem; padding: 1.5rem; margin-bottom: 2rem; border: none; box-shadow: var(--shadow-soft); border: 1px solid rgba(255,255,255,0.1); }
        .chart-container { background: white; border-radius: 1.5rem; padding: 2rem; border: none; box-shadow: var(--shadow-soft); margin-top: 2rem; }
        
        /* Variants */
        .card-debt .stat-icon { background: #fee2e2; color: #dc2626; }
        .card-stock .stat-icon { background: #e0f2fe; color: #0284c7; }
        .card-sales .stat-icon { background: #f0fdf4; color: #16a34a; }
        
        @media (max-width: 992px) { .content-area { margin-left: 0; } }
    </style>
</head>
<body class="bg-main">
    <div class="main-layout">
        <?php include_once '../components/Sidebar.php'; ?>
        
        <div class="content-area">
            <header class="mb-5">
                <h1 style="font-weight: 800; color: var(--text-main); letter-spacing: -0.025em;">Panel de Estadísticas</h1>
                <p style="color: var(--text-muted); font-size: 1.1rem;">Visión general del rendimiento y salud financiera del negocio.</p>
            </header>

            <!-- KPI Cards -->
            <div class="stats-grid">
                <div class="stat-card card-debt">
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                    </div>
                    <div class="stat-value">$<?php echo number_format($totalDebt, 2); ?></div>
                    <div class="stat-label">Deuda Total Clientes</div>
                </div>

                <div class="stat-card card-stock">
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                    </div>
                    <div class="stat-value">$<?php echo number_format($totalStockValue, 2); ?></div>
                    <div class="stat-label">Valor del Stock (Costo)</div>
                </div>

                <?php if ($selectedYear): ?>
                <div class="stat-card card-sales">
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                    </div>
                    <div class="stat-value">$<?php echo number_format($totalSales, 2); ?></div>
                    <div class="stat-label">Ventas en el Período</div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Filter Section -->
            <div class="filter-card">
                <h5 class="mb-4" style="font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    Filtro de Ventas
                </h5>
                <form method="GET" action="statistics.php">
                    <div class="row items-end">
                        <div class="col-md-5">
                            <label class="small font-weight-bold text-muted uppercase mb-2 block">Seleccionar Año</label>
                            <select name="year" class="modern-input w-100">
                                <option value="">Todos los años</option>
                                <?php foreach ($years as $year): ?>
                                    <option value="<?php echo $year; ?>" <?php echo $selectedYear == $year ? 'selected' : ''; ?>><?php echo $year; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="small font-weight-bold text-muted uppercase mb-2 block">Seleccionar Mes</label>
                            <select name="month" class="modern-input w-100">
                                <option value="">Todos los meses</option>
                                <?php foreach ($months as $key => $month): ?>
                                    <option value="<?php echo $key; ?>" <?php echo $selectedMonth == $key ? 'selected' : ''; ?>><?php echo $month; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="modern-btn modern-btn-primary w-100 mt-4">Filtrar</button>
                        </div>
                    </div>
                </form>
                <?php if ($selectedYear): ?>
                    <div class="mt-3 text-right">
                        <a href="statistics.php" class="text-muted small font-weight-bold">Limpiar filtros</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Chart Section -->
            <?php if (!empty($chartLabels) && !empty($chartValues)): ?>
            
            <?php if ($selectedYear): 
                $profitSummary = $saleController->getMonthlyProfitSummary($selectedYear, $selectedMonth);
                $revenue = $profitSummary['revenue'];
                $cogs = $profitSummary['cogs'];
                $opCosts = $profitSummary['operating_costs'];
                $grossMargin = $revenue - $cogs;
                $netProfit = $grossMargin - $opCosts;
                $profitPercentage = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;
                
                $periodLabel = $selectedMonth ? $months[$selectedMonth] . " " . $selectedYear : "Año " . $selectedYear;
            ?>
            <!-- Profitability Summary Container -->
            <div class="chart-container mb-4" style="border-left: 5px solid #10b981; background: linear-gradient(to right, #ffffff, #f0fdf4);">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 style="font-weight: 800; color: var(--text-main); margin: 0;">Análisis de Rentabilidad - <?php echo $periodLabel; ?></h4>
                    <div class="badge <?php echo $netProfit >= 0 ? 'badge-success' : 'badge-danger'; ?> px-4 py-2" style="font-size: 0.9rem; border-radius: 2rem;">
                        Utilidad Neta: $<?php echo number_format($netProfit, 2); ?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="p-3 rounded-lg border bg-white mb-3">
                            <span class="small font-weight-bold text-muted uppercase d-block mb-1">Ingresos Brutos</span>
                            <span class="h5 font-weight-bold" style="color: #1e293b;">$<?php echo number_format($revenue, 2); ?></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 rounded-lg border bg-white mb-3">
                            <span class="small font-weight-bold text-muted uppercase d-block mb-1">Costo Mercadería (COGS)</span>
                            <span class="h5 font-weight-bold text-danger">-$<?php echo number_format($cogs, 2); ?></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 rounded-lg border bg-white mb-3">
                            <span class="small font-weight-bold text-muted uppercase d-block mb-1">Gastos Operativos</span>
                            <span class="h5 font-weight-bold text-danger">-$<?php echo number_format($opCosts, 2); ?></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 rounded-lg border bg-white mb-3" style="background: <?php echo $netProfit >= 0 ? '#ecfdf5' : '#fef2f2'; ?>;">
                            <span class="small font-weight-bold text-muted uppercase d-block mb-1">Margen Neto</span>
                            <span class="h5 font-weight-bold" style="color: <?php echo $netProfit >= 0 ? '#059669' : '#dc2626'; ?>;">
                                <?php echo number_format($profitPercentage, 1); ?>%
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-2 p-3 rounded-lg" style="background: #f8fafc; border: 1px dashed #e2e8f0;">
                    <div class="d-flex align-items-center gap-2 text-muted small">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                        <span>La utilidad neta se calcula restando el costo de los productos vendidos y los gastos operativos del mes a los ingresos totales de ventas.</span>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="chart-container">
                <h4 class="mb-4" style="font-weight: 700; color: var(--text-main);"><?php echo $chartTitle; ?></h4>
                <div style="height: 400px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const ctx = document.getElementById('salesChart').getContext('2d');
                    
                    // Create gradient
                    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
                    gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: <?php echo json_encode($chartLabels); ?>,
                            datasets: [{
                                label: 'Ventas ($)',
                                data: <?php echo json_encode($chartValues); ?>,
                                borderColor: '#3b82f6',
                                borderWidth: 3,
                                backgroundColor: gradient,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#3b82f6',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#0f172a',
                                    padding: 12,
                                    titleFont: { size: 14, weight: 'bold' },
                                    bodyFont: { size: 14 },
                                    callbacks: {
                                        label: function(context) {
                                            return ' Ventas: $' + context.raw.toLocaleString('es-AR', {minimumFractionDigits: 2});
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: { color: '#64748b', font: { weight: '600' } }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#f1f5f9' },
                                    ticks: {
                                        color: '#64748b',
                                        font: { weight: '600' },
                                        callback: function(value) { return '$' + value.toLocaleString(); }
                                    }
                                }
                            }
                        }
                    });
                });
            </script>
            <?php else: ?>
            <div class="text-center py-5 chart-container">
                <div class="opacity-25 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
                </div>
                <h5 class="text-muted">No hay datos para mostrar</h5>
                <p class="text-muted small">Selecciona un año y mes para visualizar la gráfica de ventas.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</body>
</html>
