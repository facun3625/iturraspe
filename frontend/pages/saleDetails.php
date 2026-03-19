<?php
// Ruta: control_stock/frontend/pages/saleDetails.php

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

if (!isset($_GET['sale_id'])) {
    die("ID de venta no especificado.");
}

$sale_id = $_GET['sale_id'];
$saleController = new SaleController();
$saleDetails = $saleController->getSaleDetails($sale_id);

if (!$saleDetails) {
    die("No se encontró la venta especificada.");
}

// Asignación de los valores necesarios
$totalPaid = $saleDetails['amount_paid'] ?? 0; // Total pagado
$totalDebt = $saleDetails['sale_total'] - $totalPaid; // Diferencia para obtener la deuda
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Venta</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <!-- Barra de navegación -->
    <?php include_once '../components/navbar.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center">Detalle de Venta #<?php echo $sale_id; ?></h2>
        
        <!-- Información de la venta -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Información de la Venta</h5>
                <p><strong>Cliente:</strong> <?php echo $saleDetails['client_name']; ?></p>
                <p><strong>Fecha de Venta:</strong> <?php echo $saleDetails['sale_date']; ?></p>
                <p style="color: red;"><strong>Total de la Venta:</strong> $<?php echo number_format($saleDetails['sale_total'], 2); ?></p>
                <p><strong>Total Pagado:</strong> $<?php echo number_format($totalPaid, 2); ?></p>
                <p><strong>Total Deuda:</strong> $<?php echo number_format($totalDebt, 2); ?></p>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="d-flex justify-content-between mb-4">
            <div>
                <button class="btn btn-info" onclick="window.history.back();">Volver atrás</button>
                <a href="salesEdit.php?sale_id=<?php echo $sale_id; ?>" class="btn btn-warning">Editar Venta</a>
            </div>
            <button id="printPdfBtn" class="btn btn-danger">Imprimir en PDF</button>
        </div>

        <!-- Detalle de los productos vendidos -->
        <h5>Productos Vendidos</h5>
        <table class="table table-bordered" id="saleDetailsTable">
            <thead>
                <tr>
                    <th>ID Producto</th>
                    <th>Nombre</th>
                    <th>Cantidad Vendida</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($saleDetails['items'] as $item): ?>
                    <tr>
                        <td><?php echo $item['product_id']; ?></td>
                        <td><?php echo $item['product_name']; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td>$<?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable/dist/jspdf.plugin.autotable.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('printPdfBtn').addEventListener('click', function() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Título del PDF
        doc.text(`Detalle de Venta #<?php echo $sale_id; ?>`, 14, 10);

        // Información de la venta
        doc.text(`Cliente: <?php echo $saleDetails['client_name']; ?>`, 14, 20);
        doc.text(`Fecha de Venta: <?php echo $saleDetails['sale_date']; ?>`, 14, 30);
        doc.text(`Total de la Venta: $<?php echo number_format($saleDetails['sale_total'], 2); ?>`, 14, 40);
        doc.text(`Total Pagado: $<?php echo number_format($totalPaid, 2); ?>`, 14, 50);
        doc.text(`Total Deuda: $<?php echo number_format($totalDebt, 2); ?>`, 14, 60);

        // Generar tabla de productos vendidos
        const tableColumns = ['ID Producto', 'Nombre', 'Cantidad Vendida', 'Precio Unitario', 'Subtotal'];
        const tableRows = <?php echo json_encode(array_map(function($item) {
            return [
                $item['product_id'],
                $item['product_name'],
                $item['quantity'],
                '$' . number_format($item['price'], 2),
                '$' . number_format($item['quantity'] * $item['price'], 2)
            ];
        }, $saleDetails['items'])); ?>;

        doc.autoTable({
            startY: 70,
            head: [tableColumns],
            body: tableRows
        });

        // Obtener la posición final de la tabla para colocar el nuevo contenido debajo
        const finalY = doc.lastAutoTable.finalY || 70;

        // Agregar nuevamente la fecha y el total de la venta debajo de la tabla
        doc.text(`Fecha de Venta: <?php echo $saleDetails['sale_date']; ?>`, 14, finalY + 10);
        doc.text(`Total de la Venta: $<?php echo number_format($saleDetails['sale_total'], 2); ?>`, 14, finalY + 20);

        // Guardar el PDF
        doc.save('detalle_venta_<?php echo $sale_id; ?>.pdf');
    });
</script>

</body>
</html>
