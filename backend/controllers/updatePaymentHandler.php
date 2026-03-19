<?php
// Ruta: control_stock/backend/controllers/updatePaymentHandler.php

require_once '../../backend/config/database.php'; // Incluye la conexión a la base de datos
require_once 'saleController.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar si se recibieron los datos necesarios
    if (isset($_POST['sale_id']) && isset($_POST['edit_payment_amount'])) {
        $saleId = $_POST['sale_id'];
        $newAmountPaid = $_POST['edit_payment_amount'];

        // Conectar a la base de datos
        $db = new Database();
        $conn = $db->getConnection();

        // Preparar la consulta para actualizar el monto pagado
        $query = "UPDATE sales SET amount_paid = :amount_paid WHERE id = :sale_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':amount_paid', $newAmountPaid, PDO::PARAM_STR);
        $stmt->bindParam(':sale_id', $saleId, PDO::PARAM_INT);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Redirigir a la página de ventas del cliente con mensaje de éxito
            $clientId = $_POST['client_id'];
            header("Location: ../../frontend/pages/clientSales.php?client_id=$clientId&success=1");
            exit();
        } else {
            echo "Error al actualizar el pago.";
        }
    } else {
        echo "Faltan datos necesarios para actualizar el pago.";
    }
} else {
    echo "Método de solicitud no válido.";
}
?>
