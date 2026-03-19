<?php
require_once 'backend/config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();

    if ($conn) {
        echo "Conexión exitosa a la base de datos.<br>";

        // Ejecuta la consulta para obtener los productos
        $query = "SELECT * FROM products";
        $stmt = $conn->prepare($query);
        $stmt->execute();

        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($products)) {
            echo "<pre>";
            print_r($products);
            echo "</pre>";
        } else {
            echo "No se encontraron productos en la base de datos.";
        }
    }
} catch (PDOException $exception) {
    echo "Error de conexión o consulta: " . $exception->getMessage();
}
