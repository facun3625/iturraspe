<?php
// Ruta: control_stock/backend/controllers/registerPaymentHandler.php

// Mostrar errores para debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../backend/config/database.php';

class RegisterPaymentHandler {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function registerPayment($sale_id, $payment_amount) {
        // Establecer la fecha actual
        $payment_date = date('Y-m-d');

        // Iniciar una transacción para asegurar la integridad de los datos
        $this->conn->beginTransaction();

        try {
            // Insertar el pago en la tabla de pagos
            $query = "INSERT INTO payments (sale_id, payment_amount, payment_date) VALUES (:sale_id, :payment_amount, :payment_date)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':sale_id', $sale_id);
            $stmt->bindParam(':payment_amount', $payment_amount);
            $stmt->bindParam(':payment_date', $payment_date);
            $stmt->execute();

            // Actualizar la cantidad pagada en la tabla de ventas
            $updateQuery = "UPDATE sales SET amount_paid = amount_paid + :payment_amount WHERE id = :sale_id";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(':payment_amount', $payment_amount);
            $updateStmt->bindParam(':sale_id', $sale_id);
            $updateStmt->execute();

            // Confirmar la transacción
            $this->conn->commit();

            // Redirigir de vuelta a clientSales.php con el ID del cliente
            $client_id = $_POST['client_id'];
            header("Location: ../../frontend/pages/clientSales.php?client_id=$client_id&success=1");
            exit();

        } catch (Exception $e) {
            // Revertir cambios en caso de error
            $this->conn->rollBack();
            echo json_encode(['success' => false, 'message' => 'Error al registrar el pago: ' . $e->getMessage()]);
        }
    }
}

// Verificar si los datos se enviaron correctamente
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sale_id = $_POST['sale_id'];
    $payment_amount = $_POST['payment_amount'];
    $client_id = $_POST['client_id'];

    $handler = new RegisterPaymentHandler();
    $handler->registerPayment($sale_id, $payment_amount);
}
