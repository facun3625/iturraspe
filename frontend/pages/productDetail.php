<?php
// Ruta: control_stock/frontend/pages/productDetail.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../backend/controllers/productController.php';

$productController = new ProductController();



// Validar el ID del producto
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Producto no encontrado.";
    exit();
}

$productId = $_GET['id'];

try {
    $product = $productController->getProductById($productId); // Implementa esta función en tu controlador
    if (!$product) {
        echo "Producto no encontrado.";
        exit();
    }
} catch (Exception $e) {
    echo "Error al cargar el producto: " . $e->getMessage();
    exit();
    
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha del Producto</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>


    <div class="container mt-5">
        <h2 class="text-center">Imagen Producto</h2>
        <div class="card mt-4">
            <div class="card-header">
                <h4><?php echo htmlspecialchars($product['name']); ?></h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <?php if (!empty($product['image_url'])): ?>
                            <img src="../<?php echo htmlspecialchars($product['image_url']); ?>" alt="Imagen del producto" class="img-fluid">
                        <?php else: ?>
                            <p>No hay imagen disponible.</p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-8">

                       
                        
                        <p><strong>Descripción:</strong> <?php echo htmlspecialchars($product['description'] ?? 'No disponible.'); ?></p>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
