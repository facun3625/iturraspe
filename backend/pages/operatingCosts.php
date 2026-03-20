<?php
// Ruta: control_stock/frontend/pages/operatingCosts.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../backend/controllers/authController.php';
$authController = new AuthController();

if (!$authController->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

require_once '../../backend/controllers/operatingCostController.php';

$controller = new OperatingCostController();

$year = $_GET['year'] ?? date('Y');
$month = $_GET['month'] ?? date('m');

$report = $controller->getMonthlyReport($year, $month);
$costs = $report['costs'];
$totalMonthly = $report['total'];

$monthNames = [
    '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
    '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
    '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
];

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Costos Operativos | Julio Iturraspe</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/modern-system.css">
    <style>
        .main-layout { display: flex; min-height: 100vh; }
        .content-area { flex: 1; margin-left: 260px; padding: 2.5rem; background-color: #f8fafc; transition: all 0.3s; }
        
        .header-card { background: white; border-radius: 1.5rem; padding: 2rem; margin-bottom: 2rem; box-shadow: var(--shadow-soft); display: flex; justify-content: space-between; align-items: center; border-left: 5px solid #6366f1; }
        .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .summary-card { background: white; padding: 1.5rem; border-radius: 1.25rem; box-shadow: var(--shadow-soft); border: 1px solid rgba(0,0,0,0.02); }
        
        .cost-table-card { background: white; border-radius: 1.5rem; padding: 2rem; box-shadow: var(--shadow-soft); }
        .category-badge { padding: 0.4rem 0.8rem; border-radius: 2rem; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; }
        
        @media (max-width: 1200px) { .content-area { margin-left: 0; } }
    </style>
</head>
<body class="bg-main">
    <div class="main-layout">
        <?php include_once '../components/Sidebar.php'; ?>

        <div class="content-area">
            <div class="header-card">
                <div>
                    <span class="badge badge-indigo px-3 py-1 mb-2" style="background: #e0e7ff; color: #4338ca; border-radius: 2rem; font-size: 0.7rem; font-weight: 700;">GESTIÓN DE GASTOS</span>
                    <h1 style="font-weight: 800; color: var(--text-main); margin: 0; font-size: 1.75rem;">Costos Operativos</h1>
                </div>
                <div class="d-flex gap-3 align-items-center">
                    <form class="form-inline bg-white p-2 rounded-pill shadow-sm border">
                        <select name="month" class="form-control border-0 bg-transparent font-weight-bold" onchange="this.form.submit()">
                            <?php foreach ($monthNames as $m => $name): ?>
                                <option value="<?php echo $m; ?>" <?php echo $m == $month ? 'selected' : ''; ?>><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="year" class="form-control border-0 bg-transparent font-weight-bold" onchange="this.form.submit()">
                            <?php for($i = date('Y'); $i >= 2024; $i--): ?>
                                <option value="<?php echo $i; ?>" <?php echo $i == $year ? 'selected' : ''; ?>><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </form>
                    <button class="modern-btn modern-btn-primary" style="width: auto; padding: 0.75rem 1.5rem;" data-toggle="modal" data-target="#addCostModal">
                        + Nuevo Gasto
                    </button>
                </div>
            </div>

            <div class="summary-grid">
                <div class="summary-card" style="border-bottom: 4px solid #6366f1;">
                    <span style="font-size: 0.875rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Total <?php echo $monthNames[$month]; ?></span>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div style="font-size: 2rem; font-weight: 800; color: #4338ca;">$<?php echo number_format($totalMonthly, 2); ?></div>
                        <div style="background: #eef2ff; color: #6366f1; padding: 12px; border-radius: 12px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="cost-table-card">
                <div class="table-responsive">
                    <table class="table" id="costsTable">
                        <thead>
                            <tr style="border-bottom: 2px solid #f1f5f9;">
                                <th style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">FECHA</th>
                                <th style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">CATEGORÍA</th>
                                <th style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">DESCRIPCIÓN</th>
                                <th class="text-right" style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">MONTO</th>
                                <th class="text-right" style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($costs)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="opacity-50 mb-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                        </div>
                                        <p style="color: var(--text-muted); font-weight: 500;">No hay gastos registrados en este mes.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($costs as $cost): ?>
                                <tr style="border-bottom: 1px solid #f8fafc;">
                                    <td style="font-weight: 600; color: #64748b;"><?php echo date('d/m/Y', strtotime($cost['date'])); ?></td>
                                    <td>
                                        <?php 
                                            $catColor = '#64748b';
                                            $catBg = '#f1f5f9';
                                            switch($cost['category']) {
                                                case 'Alquiler': $catColor = '#9d174d'; $catBg = '#fce7f3'; break;
                                                case 'Luz': $catColor = '#854d0e'; $catBg = '#fef9c3'; break;
                                                case 'Flete': $catColor = '#1e40af'; $catBg = '#dbeafe'; break;
                                                case 'Servicios': $catColor = '#065f46'; $catBg = '#d1fae5'; break;
                                            }
                                        ?>
                                        <span class="category-badge" style="color: <?php echo $catColor; ?>; background: <?php echo $catBg; ?>;"><?php echo $cost['category']; ?></span>
                                    </td>
                                    <td style="font-weight: 500; color: var(--text-main);"><?php echo htmlspecialchars($cost['description']); ?></td>
                                    <td class="text-right" style="font-weight: 800; color: #ef4444; font-size: 1rem;">$<?php echo number_format($cost['amount'], 2); ?></td>
                                    <td class="text-right">
                                        <button onclick="confirmDelete(<?php echo $cost['id']; ?>)" class="btn btn-link text-danger p-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Cost Modal -->
    <div class="modal fade" id="addCostModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 1.5rem; border: none; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="modal-title font-weight-bold" style="font-size: 1.25rem;">Registrar Nuevo Gasto</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="../../backend/controllers/addOperatingCostHandler.php" method="POST">
                    <div class="modal-body p-4">
                        <div class="form-group mb-3">
                            <label style="font-weight: 700; color: var(--text-main); font-size: 0.85rem;">Categoría</label>
                            <select name="category" class="form-control" style="border-radius: 0.75rem; border: 1px solid #e2e8f0; height: 3rem; font-weight: 600;">
                                <option value="Otros">Otros</option>
                                <option value="Alquiler">Alquiler</option>
                                <option value="Luz">Luz</option>
                                <option value="Flete">Flete</option>
                                <option value="Servicios">Servicios</option>
                                <option value="Sueldos">Sueldos</option>
                                <option value="Impuestos">Impuestos</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label style="font-weight: 700; color: var(--text-main); font-size: 0.85rem;">Descripción</label>
                            <input type="text" name="description" class="form-control" style="border-radius: 0.75rem; border: 1px solid #e2e8f0; height: 3rem;" placeholder="Ej: Pago de luz marzo" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label style="font-weight: 700; color: var(--text-main); font-size: 0.85rem;">Monto</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem; border: 1px solid #e2e8f0;">$</span></div>
                                    <input type="number" step="0.01" name="amount" class="form-control" style="border-radius: 0 0.75rem 0.75rem 0; border: 1px solid #e2e8f0; height: 3rem; font-weight: 700;" required>
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label style="font-weight: 700; color: var(--text-main); font-size: 0.85rem;">Fecha</label>
                                <input type="date" name="date" class="form-control" style="border-radius: 0.75rem; border: 1px solid #e2e8f0; height: 3rem;" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="modern-btn" data-dismiss="modal" style="width: auto; background: #f1f5f9; color: #64748b;">Cancelar</button>
                        <button type="submit" class="modern-btn modern-btn-primary" style="width: auto; background: #6366f1;">Guardar Gasto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(id) {
            if (confirm('¿Estás seguro de que deseas eliminar este gasto?')) {
                window.location.href = '../../backend/controllers/deleteOperatingCostHandler.php?id=' + id;
            }
        }
    </script>
</body>
</html>
