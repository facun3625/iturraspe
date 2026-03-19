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
    <title>Reporte de Ventas por Producto</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include_once '../components/navbar.php'; ?>

    <div class="container mt-4">
        <h4 class="text-center mb-4">Reporte de Ventas por Producto</h4>
        <form method="POST" class="form-inline justify-content-center mb-4">
            <div class="form-group mx-sm-3 mb-2">
                <label for="start_date" class="sr-only">Desde</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?= htmlspecialchars($startDate) ?>" required>
            </div>
            <div class="form-group mx-sm-3 mb-2">
                <label for="end_date" class="sr-only">Hasta</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?= htmlspecialchars($endDate) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary mb-2">Filtrar</button>
        </form>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $startDate && $endDate): ?>
            <h5 class="mb-3 text-center">
                Mostrando ventas desde <b><?= htmlspecialchars($startDate) ?></b> hasta <b><?= htmlspecialchars($endDate) ?></b>
            </h5>
        <?php endif; ?>

        <?php if (!empty($results)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Producto</th>
                            <th>Precio Unitario</th>
                            <th>Cantidad Vendida</th>
                            <th>Total Vendido</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['product_name']) ?></td>
                                <td>$<?= number_format($row['price_unit'], 2) ?></td>
                                <td><?= $row['total_quantity'] ?></td>
                                <td>$<?= number_format($row['total_amount'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="alert alert-warning text-center">No se encontraron ventas en ese período.</div>
        <?php endif; ?>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
     <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
