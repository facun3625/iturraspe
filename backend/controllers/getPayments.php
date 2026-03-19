<?php

// Activar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ruta: control_stock/backend/controllers/productController.php
require_once __DIR__ . '/../config/database.php';

class PaymentController {
    private $conn;

    public function __construct() {
        $database = new Database(); // Instancia de la clase Database
        $this->conn = $database->getConnection(); // Llamar al método no estático
    }

    public function getAllPayments() {
        $query = "
    SELECT 
        p.id AS payment_id,
        s.client_id,
        s.id AS sale_id,
        s.total,
        p.payment_amount,
        p.payment_date,
        c.name AS client_name  -- Cambié 'c.client_name' a 'c.name'
    FROM payments p
    JOIN sales s ON p.sale_id = s.id
    JOIN clients c ON s.client_id = c.id
";


        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


?>
