<?php
// Ruta: control_stock/frontend/pages/payments.php

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

require_once '../../backend/controllers/getPayments.php';

$paymentController = new PaymentController();
$payments = $paymentController->getAllPayments();
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagos | Julio Iturraspe</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../assets/css/modern-system.css">
    <style>
        .main-layout { display: flex; min-height: 100vh; }
        .content-area { flex: 1; margin-left: 260px; padding: 2rem; background-color: #f8fafc; }
        .table-card { background: white; border-radius: 1rem; padding: 2rem; border: none; box-shadow: var(--shadow-soft); }
        .amount-badge { background: #f0fdf4; color: #166534; padding: 0.4rem 0.6rem; border-radius: 0.5rem; font-weight: 700; }
        .date-text { color: #64748b; font-size: 0.875rem; }
        .client-name { font-weight: 700; color: var(--text-main); }
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
                        <h2 style="font-weight: 700; color: var(--text-main); margin: 0;">Pagos Recibidos</h2>
                        <p style="color: var(--text-muted); margin: 0;">Seguimiento detallado de todos los cobros a clientes.</p>
                    </div>
                    <a href="clientListDeb.php" class="modern-btn modern-btn-primary" style="width: auto;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Cobrar a Cliente
                    </a>
                </div>

                <div class="table-responsive">
                    <table id="paymentsTable" class="table">
                        <thead>
                            <tr>
                                <th>ID Pago</th>
                                <th>Venta Ref.</th>
                                <th>Cliente</th>
                                <th class="text-center">Monto</th>
                                <th class="text-right">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td style="font-weight: 600; color: var(--text-muted);">#<?php echo $payment['payment_id']; ?></td>
                                    <td><span class="badge badge-light">Venta #<?php echo $payment['sale_id']; ?></span></td>
                                    <td>
                                        <div class="client-name"><?php echo $payment['client_name']; ?></div>
                                        <small class="text-muted">ID: <?php echo $payment['client_id']; ?></small>
                                    </td>
                                    <td class="text-center">
                                        <span class="amount-badge">$<?php echo number_format($payment['payment_amount'], 2); ?></span>
                                    </td>
                                    <td class="text-right date-text">
                                        <?php echo date('d/m/Y H:i', strtotime($payment['payment_date'])); ?>
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
            $('#paymentsTable').DataTable({
                "order": [[4, "desc"]],
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
</body>
</html>
