<?php
// Ruta: control_stock/frontend/pages/productListLowStock.php

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

require_once '../../backend/controllers/productController.php';
$productController = new ProductController();

try {
    $products = $productController->getLowStockProducts(); // Llamada al nuevo método
} catch (Exception $e) {
    $products = [];
    $error = "Error al cargar los productos en alerta de stock: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alertas de Stock | Julio Iturraspe</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../assets/css/modern-system.css">
    <style>
        .main-layout { display: flex; min-height: 100vh; }
        .content-area { flex: 1; margin-left: 260px; padding: 2rem; background-color: #f8fafc; }
        .table-card { background: white; border-radius: 1rem; padding: 2rem; border: none; box-shadow: var(--shadow-soft); }
        table.dataTable { border-collapse: separate !important; border-spacing: 0 0.5rem !important; }
        table.dataTable tbody tr { background-color: #fff !important; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border-radius: 0.5rem; }
        table.dataTable tbody td { border: none !important; padding: 1rem !important; vertical-align: middle !important; }
        table.dataTable thead th { border-bottom: 2px solid #f1f5f9 !important; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; }
        .badge-alert { padding: 0.4rem 0.6rem; border-radius: 0.5rem; font-weight: 700; background: #fee2e2; color: #991b1b; }
        @media (max-width: 992px) { .content-area { margin-left: 0; } }
    </style>
</head>
<body class="bg-main">
    <div class="main-layout">
        <?php include_once '../components/Sidebar.php'; ?>
        
        <div class="content-area">
            <div class="table-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 style="font-weight: 700; color: var(--text-main); margin: 0;">Alertas de Stock</h2>
                        <p style="color: var(--text-muted); margin: 0;">Productos que han alcanzado el nivel crítico de inventario.</p>
                    </div>
                    <button id="printPdfBtn" class="modern-btn" style="background: #ef4444; width: auto;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M12 18v-6"/><path d="m9 15 3 3 3-3"/></svg>
                        Exportar Alertas
                    </button>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" style="border-radius: 0.75rem; border: none; background: #fef2f2; color: #991b1b;"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table id="productTable" class="table">
                        <thead>
                            <tr>
                                <th>Cod.</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th class="text-center">Stock Actual</th>
                                <th class="text-center">Nivel Alerta</th>
                                <th>División</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td style="font-weight: 600; color: var(--text-muted);"><?php echo $product['cod']; ?></td>
                                    <td style="font-weight: 700; color: #dc2626;"><?php echo $product['name']; ?></td>
                                    <td><span class="badge" style="background: #f1f5f9; color: #475569;"><?php echo $product['category_name']; ?></span></td>
                                    <td class="text-center"><span class="badge-alert"><?php echo $product['stock']; ?></span></td>
                                    <td class="text-center" style="font-weight: 600; color: #64748b;"><?php echo $product['low_stock_level']; ?></td>
                                    <td><?php echo number_format($product['division'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable/dist/jspdf.plugin.autotable.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#productTable').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"
                },
                "order": [[5, 'asc']],
                "columnDefs": [
                    { "targets": 5, "visible": false }
                ]
            });

            $('#printPdfBtn').on('click', function() {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();
                doc.text('Productos en Alerta de Stock', 14, 10);
                const tableColumns = ['ID', 'Nombre', 'Categoría', 'Stock', 'Alerta'];
                const tableRows = <?php echo json_encode(array_map(function($product) {
                    return [$product['cod'], $product['name'], $product['category_name'], $product['stock'], $product['low_stock_level']];
                }, $products)); ?>;
                doc.autoTable({
                    head: [tableColumns],
                    body: tableRows,
                    startY: 20
                });
                doc.save('alertas_stock_iturraspe.pdf');
            });
        });
    </script>
</body>
</body>
</html>
