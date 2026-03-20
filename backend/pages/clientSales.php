<?php
// Ruta: control_stock/frontend/pages/clientSales.php

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

$clientId = $_GET['client_id'];
$saleController = new SaleController();
$sales = $saleController->getSalesByClientId($clientId); // Obtiene las ventas del cliente

// Calcular el resumen
$totalSales = count($sales);
$totalPaid = array_sum(array_column($sales, 'amount_paid'));
$totalDebt = array_sum(array_map(function($sale) {
    return $sale['sale_total'] - $sale['amount_paid'];
}, $sales));

// Obtener el nombre del cliente de una de las ventas (si existe)
$clientName = $sales ? $sales[0]['client_name'] : 'Cliente';




?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de Cuenta | <?php echo htmlspecialchars($clientName); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../assets/css/modern-system.css">
    <style>
        .main-layout { display: flex; min-height: 100vh; }
        .content-area { flex: 1; margin-left: 260px; padding: 2.5rem; background-color: #f8fafc; transition: all 0.3s; }
        
        .client-header { background: white; border-radius: 1.5rem; padding: 2rem; margin-bottom: 2rem; box-shadow: var(--shadow-soft); display: flex; justify-content: space-between; align-items: center; border-left: 5px solid var(--primary); }
        .client-info h1 { font-weight: 800; color: var(--text-main); margin: 0; font-size: 1.75rem; }
        
        .stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: white; padding: 1.5rem; border-radius: 1.25rem; box-shadow: var(--shadow-soft); border: 1px solid rgba(0,0,0,0.02); }
        .stat-label { font-size: 0.875rem; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.025em; }
        .stat-value { font-size: 1.5rem; font-weight: 800; color: var(--text-main); }
        
        .table-card { background: white; border-radius: 1.5rem; padding: 2rem; box-shadow: var(--shadow-soft); border: 1px solid rgba(0,0,0,0.03); }
        
        #clientSalesTable thead th { 
            background: #f8fafc; 
            border-bottom: 2px solid #f1f5f9; 
            color: #64748b; 
            font-size: 0.75rem; 
            font-weight: 700; 
            text-transform: uppercase; 
            letter-spacing: 0.05em;
            padding: 1.25rem 1rem;
        }
        
        #clientSalesTable tbody td { 
            padding: 1.25rem 1rem; 
            vertical-align: middle;
            border-bottom: 1px solid #f8fafc;
        }
        
        #clientSalesTable tbody tr { transition: 0.2s; }
        #clientSalesTable tbody tr:hover { background-color: #fcfdfe; }
        
        .badge-status { 
            padding: 0.5rem 1rem; 
            border-radius: 2rem; 
            font-weight: 700; 
            font-size: 0.7rem; 
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        .badge-paid { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .badge-debt { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        
        .btn-action {
            height: 34px;
            padding: 0 1rem;
            border-radius: 0.75rem;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        .btn-cobrar { background: #3b82f6; color: white; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2); }
        .btn-cobrar:hover { background: #2563eb; transform: translateY(-1px); box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3); color: white; text-decoration: none; }
        .btn-detalle { background: #f1f5f9; color: #475569; }
        .btn-detalle:hover { background: #e2e8f0; color: #1e293b; text-decoration: none; }
        
        /* DataTables Custom */
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0.5rem 1rem;
            margin-left: 0.75rem;
            outline: none;
            font-weight: 500;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary) !important;
            color: white !important;
            border: none !important;
            border-radius: 0.5rem !important;
        }
        
        /* Fix DataTables Header Icons */
        table.dataTable thead .sorting,
        table.dataTable thead .sorting_asc,
        table.dataTable thead .sorting_desc {
            background-image: none !important;
        }
        table.dataTable thead .sorting:after,
        table.dataTable thead .sorting_asc:after,
        table.dataTable thead .sorting_desc:after {
            opacity: 0.5 !important;
            font-size: 0.8rem !important;
            right: 0.5rem !important;
        }
        table.dataTable thead .sorting:before,
        table.dataTable thead .sorting_asc:before,
        table.dataTable thead .sorting_desc:before {
            display: none !important;
        }
        
        @media (max-width: 1200px) { .content-area { margin-left: 0; } .stat-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body class="bg-main">
    <div class="main-layout">
        <?php include_once '../components/Sidebar.php'; ?>

        <div class="content-area">
            <!-- Header section -->
            <div class="client-header">
                <div class="client-info">
                    <span class="badge badge-primary px-3 py-1 mb-2" style="border-radius: 2rem; font-size: 0.7rem; text-transform: uppercase; font-weight: 700;">Cuenta Corriente</span>
                    <h1><?php echo htmlspecialchars($clientName); ?></h1>
                </div>
                <div class="d-flex gap-2">
                    <button class="modern-btn" onclick="window.history.back();" style="width: auto; background: white; color: var(--text-muted); border: 1px solid #e2e8f0; padding: 0.6rem 1.2rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="m15 18-6-6 6-6"/></svg>
                        Volver
                    </button>
                    <button id="printPdfBtn" class="modern-btn modern-btn-primary" style="width: auto; padding: 0.6rem 1.2rem; background: #ef4444;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        PDF
                    </button>
                </div>
            </div>

            <!-- Stats Summary -->
            <div class="stat-grid">
                <div class="stat-card">
                    <span class="stat-label">Ventas Realizadas</span>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="stat-value"><?php echo $totalSales; ?></div>
                        <div style="background: #eff6ff; color: #3b82f6; padding: 10px; border-radius: 12px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Total Pagado</span>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="stat-value" style="color: #10b981;">$<?php echo number_format($totalPaid, 2); ?></div>
                        <div style="background: #f0fdf4; color: #10b981; padding: 10px; border-radius: 12px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="m17 5-5-3-5 3"/><path d="m17 19-5 3-5-3"/><circle cx="12" cy="12" r="3"/></svg>
                        </div>
                    </div>
                </div>
                <div class="stat-card" style="border-right: 5px solid #ef4444;">
                    <span class="stat-label">Saldo Pendiente</span>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="stat-value" style="color: #ef4444;">$<?php echo number_format($totalDebt, 2); ?></div>
                        <div style="background: #fef2f2; color: #ef4444; padding: 10px; border-radius: 12px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table section -->
            <div class="table-card">
                <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                    <div class="alert alert-success border-0 mb-4" style="background: #f0fdf4; color: #166534; border-radius: 12px; font-weight: 600;" id="successMessage">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Pago registrado con éxito.
                    </div>
                    <script>setTimeout(() => { document.getElementById('successMessage').style.display='none'; }, 2000);</script>
                <?php endif; ?>

                <table class="table" id="clientSalesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Pagado</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales as $sale): 
                            $debt = $sale['sale_total'] - $sale['amount_paid'];
                            $isPaid = $debt <= 0;
                        ?>
                             <tr>
                                <td style="font-weight: 700; color: #64748b; font-family: monospace; font-size: 0.9rem;">#<?php echo $sale['sale_id']; ?></td>
                                <td style="font-weight: 600; color: #1e293b;"><?php echo date('d/m/Y', strtotime($sale['sale_date'])); ?></td>
                                <td style="font-weight: 800; color: var(--text-main); font-size: 1.05rem;">$<?php echo number_format($sale['sale_total'], 2); ?></td>
                                <td style="font-weight: 600; color: #10b981;">$<?php echo number_format($sale['amount_paid'], 2); ?></td>
                                <td>
                                    <?php if ($isPaid): ?>
                                        <span class="badge-status badge-paid">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                            PAGADO
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-status badge-debt" title="Debe $<?php echo number_format($debt, 2); ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                            PENDIENTE
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end align-items-center" style="gap: 0.75rem;">
                                        <?php if (!$isPaid): ?>
                                            <button class="btn-action btn-cobrar" data-toggle="modal" data-target="#paymentModal<?php echo $sale['sale_id']; ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="m17 5-5-3-5 3"/><path d="m17 19-5 3-5-3"/><circle cx="12" cy="12" r="3"/></svg>
                                                Cobrar
                                            </button>
                                        <?php else: ?>
                                            <button class="btn-action btn-detalle" data-toggle="modal" data-target="#editPaymentModal<?php echo $sale['sale_id']; ?>" style="color: #64748b; font-weight: 600;">
                                                Editar Pago
                                            </button>
                                        <?php endif; ?>
                                        <a href="saleDetails.php?sale_id=<?php echo $sale['sale_id']; ?>" class="btn-action btn-detalle" title="Ver Detalle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                            Ver
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

    <!-- Modals -->
    <?php foreach ($sales as $sale): $debt = $sale['sale_total'] - $sale['amount_paid']; ?>
    <div class="modal fade" id="paymentModal<?php echo $sale['sale_id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">Registrar Pago - Venta #<?php echo $sale['sale_id']; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="../../backend/controllers/registerPaymentHandler.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="sale_id" value="<?php echo $sale['sale_id']; ?>">
                        <input type="hidden" name="client_id" value="<?php echo $clientId; ?>">
                        <div class="mb-4 text-center">
                            <span style="display: block; font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">SALDO PENDIENTE</span>
                            <span style="display: block; font-size: 2rem; font-weight: 800; color: #ef4444;">$<?php echo number_format($debt, 2); ?></span>
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 700; color: var(--text-main);">Monto a Cobrar</label>
                            <input type="number" step="0.01" class="form-control form-control-modern" name="payment_amount" value="<?php echo $debt; ?>" max="<?php echo $debt; ?>" required autofocus>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="modern-btn" data-dismiss="modal" style="width: auto; background: #f1f5f9; color: #64748b;">No, cancelar</button>
                        <button type="submit" class="modern-btn modern-btn-primary" style="width: auto;">Confirmar Pago</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editPaymentModal<?php echo $sale['sale_id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">Modificar Pago - Venta #<?php echo $sale['sale_id']; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="../../backend/controllers/updatePaymentHandler.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="sale_id" value="<?php echo $sale['sale_id']; ?>">
                        <input type="hidden" name="client_id" value="<?php echo $clientId; ?>">
                        <div class="form-group">
                            <label style="font-weight: 700; color: var(--text-main);">Monto Total Pagado</label>
                            <input type="number" step="0.01" class="form-control form-control-modern" name="edit_payment_amount" value="<?php echo $sale['amount_paid']; ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="modern-btn" data-dismiss="modal" style="width: auto; background: #f1f5f9; color: #64748b;">Cerrar</button>
                        <button type="submit" class="modern-btn modern-btn-primary" style="width: auto;">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable/dist/jspdf.plugin.autotable.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#clientSalesTable').DataTable({
                "order": [[1, "desc"]],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"
                },
                "dom": '<"d-flex justify-content-between align-items-center mb-4"f>rt<"d-flex justify-content-between align-items-center mt-4"ip>',
                "pageLength": 10
            });

            $('#printPdfBtn').on('click', function() {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();
                doc.setFontSize(18);
                doc.text(`Ventas de ${<?php echo json_encode($clientName); ?>}`, 14, 20);
                doc.setFontSize(10);
                doc.text(`Generado el: ${new Date().toLocaleDateString()}`, 14, 28);

                const tableColumns = ['ID Venta', 'Fecha', 'Total', 'Pagado', 'Deuda'];
                const tableRows = <?php echo json_encode(array_map(function($sale) {
                    return [
                        '#' . $sale['sale_id'],
                        date('d/m/Y', strtotime($sale['sale_date'])),
                        '$' . number_format($sale['sale_total'], 2),
                        '$' . number_format($sale['amount_paid'], 2),
                        '$' . number_format($sale['sale_total'] - $sale['amount_paid'], 2)
                    ];
                }, $sales)); ?>;

                doc.autoTable({
                    head: [tableColumns],
                    body: tableRows,
                    startY: 35,
                    theme: 'grid',
                    headStyles: { fillColor: [59, 130, 246] }
                });

                doc.save('ventas_cliente_<?php echo $clientId; ?>.pdf');
            });
        });
    </script>
</body>
</html>

