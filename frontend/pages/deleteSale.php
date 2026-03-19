<?php
require_once '../../backend/controllers/saleController.php';

$saleController = new SaleController();

if (isset($_GET['sale_id'])) {
    $sale_id = $_GET['sale_id'];

    try {
        $saleController->deleteSale($sale_id);
        header("Location: salesList.php?message=Venta eliminada correctamente.");
    } catch (Exception $e) {
        header("Location: salesList.php?error=Error al eliminar la venta: " . $e->getMessage());
    }
} else {
    header("Location: salesList.php?error=ID de venta no proporcionado.");
}
?>
