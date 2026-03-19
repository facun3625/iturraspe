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
    <title>Ventas de <?php echo htmlspecialchars($clientName); ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include_once '../components/navbar.php'; ?>

    <div class="rounded-container-table">
        <h4 class="text-center mb-4">Ventas de  <span class="text-primary"><?php echo htmlspecialchars($clientName); ?></span></h4>

        <!-- Mostrar mensaje de éxito si el pago fue registrado -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert alert-success text-center" id="successMessage">Pago registrado con éxito.</div>
            <script>
                setTimeout(function() {
                    document.getElementById('successMessage').style.display = 'none';
                }, 1500);
            </script>
        <?php endif; ?>

        <!-- Resumen de ventas -->
        <div class="mt-4 mb-4">
            <p><strong>Resumen:</strong></p>
            <ul>
                <li><strong>Cantidad de Ventas:</strong> <?php echo $totalSales; ?></li>
                <li><strong>Total Pagado:</strong> $<?php echo number_format($totalPaid, 2); ?></li>
                <li><strong>Total Deuda:</strong> $<?php echo number_format($totalDebt, 2); ?></li>
            </ul>
        </div>

        <!-- Botones de acción -->
        <div class="d-flex justify-content-between pb-4">
            <button class="btn btn-info btn-sm" onclick="window.history.back();">Volver atrás</button>
            <button id="printPdfBtn" class="btn btn-danger btn-sm">Imprimir en PDF</button>
        </div>

        <!-- Tabla de ventas del cliente -->
        <table class="table table-bordered" id="clientSalesTable">
            <thead>
                <tr>
                    <th>ID Venta</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Pagado</th>
                    <th>Deuda</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $sale): 
                    $debt = $sale['sale_total'] - $sale['amount_paid'];
                ?>
                    <tr>
                        <td><?php echo $sale['sale_id']; ?></td>
                        <td><?php echo $sale['sale_date']; ?></td>
                        <td>$<?php echo number_format($sale['sale_total'], 2); ?></td>
                        <td>$<?php echo number_format($sale['amount_paid'], 2); ?></td>
                        <td>$<?php echo number_format($debt, 2); ?></td>
                        <td>
                            <?php if ($debt > 0): ?>
                                <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#paymentModal<?php echo $sale['sale_id']; ?>">Registrar Pago</button>
                            <?php else: ?>
                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editPaymentModal<?php echo $sale['sale_id']; ?>">Modificar Pago</button>
                            <?php endif; ?>
                            <a href="saleDetails.php?sale_id=<?php echo $sale['sale_id']; ?>" class="btn btn-secondary btn-sm">Ver Detalle</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    
    <!-- Modal para registrar un pago -->
   <!-- Modal para registrar un pago -->
<?php foreach ($sales as $sale): ?>
<div class="modal fade" id="paymentModal<?php echo $sale['sale_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel<?php echo $sale['sale_id']; ?>" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Pago para Venta #<?php echo $sale['sale_id']; ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="../../backend/controllers/registerPaymentHandler.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="sale_id" value="<?php echo $sale['sale_id']; ?>">
                    <input type="hidden" name="client_id" value="<?php echo $clientId; ?>">
                    <div class="form-group">
                        <label for="paymentAmount<?php echo $sale['sale_id']; ?>">Monto del Pago</label>
                        <input type="number" step="0.01" class="form-control" id="paymentAmount<?php echo $sale['sale_id']; ?>" name="payment_amount" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar Pago</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

            <!-- Fin del Modal para registrar un pago -->
                 <!-- Modal para modificar un pago -->
                 <?php foreach ($sales as $sale): ?>
            <div class="modal fade" id="editPaymentModal<?php echo $sale['sale_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editPaymentModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Modificar Pago para Venta #<?php echo $sale['sale_id']; ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="../../backend/controllers/updatePaymentHandler.php" method="post">
                            <div class="modal-body">
                                <input type="hidden" name="sale_id" value="<?php echo $sale['sale_id']; ?>">
                                <input type="hidden" name="client_id" value="<?php echo $clientId; ?>">
                                <div class="form-group">
                                    <label for="editPaymentAmount">Monto Total Pagado</label>
                                    <input type="number" step="0.01" class="form-control" id="editPaymentAmount" name="edit_payment_amount" value="<?php echo $sale['amount_paid']; ?>" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <!-- Fin del Modal para modificar un pago -->
             <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable/dist/jspdf.plugin.autotable.min.js"></script>
    <script>
        $(document).ready(function() {
            // $('#clientSalesTable').DataTable({
            //     "paging": true,
            //     "searching": true,
            //     "ordering": true,
            //     "info": true,
            //     "language": {
            //         "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"
            //     }
            // });
            $('#clientSalesTable').DataTable({
    "paging": true,
    "searching": true,
    "ordering": true,
    "info": true,
    "order": [[1, "desc"]],
    "language": {
        "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"
    }
});

            // Función para generar PDF
            $('#printPdfBtn').on('click', function() {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();

                // Título del PDF
                doc.text(`Ventas de ${<?php echo json_encode($clientName); ?>}`, 14, 10);

                // Generar tabla en PDF usando autoTable
                const tableColumns = ['ID Venta', 'Fecha', 'Total', 'Pagado', 'Deuda']; // Encabezados
                const tableRows = <?php echo json_encode(array_map(function($sale) {
                    return [
                        $sale['sale_id'],
                        $sale['sale_date'],
                        '$' . number_format($sale['sale_total'], 2),
                        '$' . number_format($sale['amount_paid'], 2),
                        '$' . number_format($sale['sale_total'] - $sale['amount_paid'], 2)
                    ];
                }, $sales)); ?>;

                doc.autoTable({
                    head: [tableColumns],
                    body: tableRows,
                    startY: 20 // Espacio desde el inicio del documento
                });

                // Guardar el PDF
                doc.save('ventas_cliente_<?php echo $clientId; ?>.pdf');
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
