<?php
// Ruta: control_stock/frontend/pages/salesList.php

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

require_once '../../backend/controllers/saleController.php';

$saleController = new SaleController();
$sales = $saleController->getSalesGrouped();
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas | Julio Iturraspe</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../assets/css/modern-system.css">
    <style>
        .main-layout { display: flex; min-height: 100vh; }
        .content-area { flex: 1; margin-left: 260px; padding: 2rem; background-color: #f8fafc; }
        .table-card { background: white; border-radius: 1rem; padding: 2rem; border: none; box-shadow: var(--shadow-soft); }
        table.dataTable { border-collapse: separate !important; border-spacing: 0 0.5rem !important; }
        table.dataTable tbody tr { background-color: #fff !important; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border-radius: 0.5rem; transition: transform 0.2s; }
        table.dataTable tbody tr:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.07); }
        table.dataTable tbody td { border: none !important; padding: 1rem !important; vertical-align: middle !important; }
        table.dataTable thead th { border-bottom: 2px solid #f1f5f9 !important; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; }
        .total-badge { background: #f0fdf4; color: #166534; padding: 0.4rem 0.6rem; border-radius: 0.5rem; font-weight: 700; }
        .date-text { color: #64748b; font-size: 0.875rem; }
        .client-text { font-weight: 700; color: var(--text-main); }
        .action-btn { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 0.5rem; transition: all 0.2s; border: none; }
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
                        <h2 style="font-weight: 700; color: var(--text-main); margin: 0;">Historial de Ventas</h2>
                        <p style="color: var(--text-muted); margin: 0;">Consulta y gestiona todas las operaciones realizadas.</p>
                    </div>
                    <a href="clientList.php" class="modern-btn modern-btn-primary" style="width: auto;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                        Nueva Venta
                    </a>
                </div>

                <!-- Mensajes -->
                <?php if (isset($_GET['message'])): ?>
                    <div class="alert alert-success" style="border-radius: 0.75rem; border: none; background: #ecfdf5; color: #065f46;"><?php echo $_GET['message']; ?></div>
                <?php endif; ?>
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger" style="border-radius: 0.75rem; border: none; background: #fef2f2; color: #991b1b;"><?php echo $_GET['error']; ?></div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table id="salesTable" class="table">
                        <thead>
                            <tr>
                                <th>ID Venta</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th class="text-center">Total</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sales as $sale): ?>
                                <tr>
                                    <td style="font-weight: 600; color: var(--text-muted);"><?php echo $sale['sale_id']; ?></td>
                                    <td class="date-text" data-order="<?php echo date('Y-m-d H:i:s', strtotime($sale['sale_date'])); ?>"><?php echo date('d/m/Y H:i', strtotime($sale['sale_date'])); ?></td>
                                    <td class="client-text"><?php echo $sale['client_name']; ?></td>
                                    <td class="text-center"><span class="total-badge">$<?php echo number_format($sale['sale_total'], 2); ?></span></td>
                                    <td class="text-right">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="saleDetails.php?sale_id=<?php echo $sale['sale_id']; ?>" class="action-btn" style="background: #eff6ff; color: #1e40af;" title="Ver Detalle">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                            </a>
                                            <a href="salesEdit.php?sale_id=<?php echo $sale['sale_id']; ?>" class="action-btn" style="background: #fef9c3; color: #854d0e;" title="Editar">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                            </a>
                                            <a href="deleteSale.php?sale_id=<?php echo $sale['sale_id']; ?>" class="action-btn" style="background: #fef2f2; color: #991b1b;" onclick="return confirm('¿Estás seguro de que deseas eliminar esta venta?');" title="Eliminar">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                            </a>
                                        </div>
                                    </td>
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
    <script>
        $(document).ready(function() {
            $('#salesTable').DataTable({
                "order": [[1, "desc"]],
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No se encontraron resultados",
                    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sSearch": "Buscar:",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "sProcessing": "Procesando...",
                },
                "dom": '<"d-flex justify-content-between align-items-center mb-4"lf>rtip'
            });
        });
    </script>
</body>
</html>
