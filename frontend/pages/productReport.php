<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../backend/controllers/authController.php';

$authController = new AuthController();
if (!$authController->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

require_once '../../backend/controllers/saleController.php';

$saleController = new SaleController();
$results = [];
$startDate = $endDate = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    if ($startDate && $endDate) {
        $results = $saleController->getProductSalesByDateRange($startDate, $endDate);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas | Iturraspe</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/modern-system.css?v=1.1">
    <style>
        .report-filter-card { max-width: 800px; margin-bottom: 2.5rem; }
        .modern-table { border: none; border-radius: 1rem; overflow: hidden; box-shadow: var(--shadow-soft); }
        .modern-table thead th { background: #1e293b; color: white; border: none; padding: 1.25rem; font-weight: 600; }
        .modern-table tbody td { padding: 1.25rem; vertical-align: middle; border-color: #f1f5f9; color: #475569; }
        .modern-table tbody tr:hover { background-color: #f8fafc; }
        .date-input-group { display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end; }
        @media (max-width: 768px) { .date-input-group { flex-direction: column; align-items: stretch; } }
    </style>
</head>
<body class="bg-main">
    <div class="main-layout">
        <?php include_once '../components/Sidebar.php'; ?>

        <div class="content-area">
            <header>
                <h1 class="header-title">Ventas por Producto</h1>
                <p class="header-subtitle">Analizá el rendimiento de tus productos en un período determinado.</p>
            </header>

            <div class="modern-card report-filter-card">
                <form method="POST" class="mb-0">
                    <div class="date-input-group">
                        <div class="form-group mb-0 flex-grow-1">
                            <label class="small font-weight-bold text-muted uppercase">Fecha Desde</label>
                            <input type="date" class="modern-input" id="start_date" name="start_date" value="<?= htmlspecialchars($startDate) ?>" required>
                        </div>
                        <div class="form-group mb-0 flex-grow-1">
                            <label class="small font-weight-bold text-muted uppercase">Fecha Hasta</label>
                            <input type="date" class="modern-input" id="end_date" name="end_date" value="<?= htmlspecialchars($endDate) ?>" required>
                        </div>
                        <button type="submit" class="modern-btn modern-btn-primary" style="width: auto; padding-left: 2rem; padding-right: 2rem;">
                            Filtrar Resultados
                        </button>
                    </div>
                </form>
            </div>

            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $startDate && $endDate): ?>
                <div class="mb-4 d-flex align-items-center">
                    <div style="width: 4px; height: 24px; background: var(--accent-blue); border-radius: 4px; margin-right: 12px;"></div>
                    <h5 class="mb-0" style="font-weight: 700; color: var(--text-main);">
                        Resultados del <span class="text-primary"><?= htmlspecialchars($startDate) ?></span> al <span class="text-primary"><?= htmlspecialchars($endDate) ?></span>
                    </h5>
                </div>
            <?php endif; ?>

            <?php if (!empty($results)): ?>
                <div class="modern-card p-0" style="overflow: hidden; border: none;">
                    <div class="table-responsive">
                        <table class="table modern-table mb-0">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio Unitario</th>
                                    <th>Cantidad</th>
                                    <th class="text-right">Total Vendido</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $grandTotalQuantity = 0;
                                $grandTotalAmount = 0;
                                foreach ($results as $row): 
                                    $grandTotalQuantity += $row['total_quantity'];
                                    $grandTotalAmount += $row['total_amount'];
                                ?>
                                    <tr>
                                        <td style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($row['product_name']) ?></td>
                                        <td>$<?= number_format($row['price_unit'], 2) ?></td>
                                        <td>
                                            <span class="badge badge-light" style="padding: 0.5rem 0.75rem; border-radius: 0.5rem; font-size: 0.9rem;">
                                                <?= $row['total_quantity'] ?> u.
                                            </span>
                                        </td>
                                        <td class="text-right" style="font-weight: 700; color: var(--accent-blue); font-size: 1.05rem;">
                                            $<?= number_format($row['total_amount'], 2) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot style="background: #f8fafc; border-top: 2px solid #e2e8f0;">
                                <tr>
                                    <td colspan="2" class="text-right" style="font-weight: 800; color: #1e293b; padding: 1.5rem;">TOTAL GENERAL</td>
                                    <td style="font-weight: 800; color: #1e293b; padding: 1.5rem;">
                                        <span class="badge badge-dark" style="padding: 0.6rem 1rem; border-radius: 0.5rem; font-size: 1rem;">
                                            <?= $grandTotalQuantity ?> u.
                                        </span>
                                    </td>
                                    <td class="text-right" style="font-weight: 900; color: var(--accent-blue); font-size: 1.25rem; padding: 1.5rem;">
                                        $<?= number_format($grandTotalAmount, 2) ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <div class="modern-card text-center py-5">
                    <div class="mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    </div>
                    <h5 style="color: #64748b;">No se encontraron ventas</h5>
                    <p class="text-muted">Probá con otro rango de fechas para ver qué productos se movieron.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
