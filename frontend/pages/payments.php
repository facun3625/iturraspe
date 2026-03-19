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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagos Recibidos</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include_once '../components/navbar.php'; ?>

    <div class="rounded-container-table">
        <h4 class="text-center mb-4">Pagos Recibidos</h4>
        <table class="table display" id="paymentsTable">
            <thead>
                <tr>
                    <th>ID de Pago</th>
                    <th>ID de Venta</th>
                    <th>ID del Cliente</th>
                    <th>Nombre del Cliente</th>
                    <th>Monto del Pago</th>
                    <th>Fecha del Pago</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><?php echo $payment['payment_id']; ?></td>
                        <td><?php echo $payment['sale_id']; ?></td>
                        <td><?php echo $payment['client_id']; ?></td>
                        <td><?php echo $payment['client_name']; ?></td>
                        <td>$<?php echo number_format($payment['payment_amount'], 2); ?></td>
                        <td><?php echo $payment['payment_date']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#paymentsTable').DataTable({
                "paging": true,          // Activar paginación
                "searching": true,       // Activar búsqueda
                "ordering": true,        // Activar ordenamiento
                "info": true,            // Mostrar información del estado
                "order": [[5, "desc"]],  // Ordenar por la columna de Fecha del Pago (índice 5) en orden descendente
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json" // Traducción al español
                }
            });
        });
    </script>
</body>
</html>
