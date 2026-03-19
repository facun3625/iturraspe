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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Ventas</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Barra de navegación -->
    <?php include_once '../components/navbar.php'; ?>

    <div class="rounded-container-table ">
        <h4 class="text-center mb-4">Listado de Ventas</h4>
        
        <!-- Mensajes de éxito o error -->
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success text-center"><?php echo $_GET['message']; ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger text-center"><?php echo $_GET['error']; ?></div>
        <?php endif; ?>

        <table class="table table-bordered display" id="salesTable">
            <thead>
                <tr>
                    <th>ID Venta</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $sale): ?>
                    <tr>
                        <td><?php echo $sale['sale_id']; ?></td>
                        <td><?php echo $sale['sale_date']; ?></td>
                        <td><?php echo $sale['client_name']; ?></td>
                        <td>$<?php echo number_format($sale['sale_total'], 2); ?></td>
                        <td>
                            <a href="saleDetails.php?sale_id=<?php echo $sale['sale_id']; ?>" class="btn btn-info btn-sm">Ver Detalle</a>
                            
    <a href="salesEdit.php?sale_id=<?php echo $sale['sale_id']; ?>" class="btn btn-warning btn-sm">Editar</a>
    

                            <a href="deleteSale.php?sale_id=<?php echo $sale['sale_id']; ?>" class="btn btn-danger btn-sm deleteBtn" onclick="return confirm('¿Estás seguro de que deseas eliminar esta venta?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
    $('#salesTable').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "order": [[1, "desc"]], // Ordena por la columna 1 (Fecha) en orden descendente
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"
        }
    });
});
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
