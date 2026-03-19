<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Ruta: control_stock/frontend/pages/dashboard.php
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

// Ruta: control_stock/frontend/pages/addProduct.php

require_once '../../backend/controllers/categoryController.php';
$categoryController = new CategoryController();

try {
    $categories = $categoryController->getCategoriesWithProductCount();
} catch (Exception $e) {
    $categories = [];
    $error = "Error al cargar las categorías: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include_once '../components/navbar.php'; ?>
<div class="rounded-container-form-2">
   
        <div>
            <h4 class="text-center mb-4">Agregar Nuevo Producto</h4>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">Producto agregado con éxito.</div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="alert alert-danger">Error al agregar el producto.</div>
            <?php endif; ?>

            <form action="../../backend/controllers/addProductHandler.php" method="post" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="category_id">Categoría</label>
                        <select class="form-control" id="category_id" name="category_id" required>
                            <option value="">Seleccione una categoría</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="cod">ID del Producto</label>
                        <input type="text" class="form-control" id="cod" name="cod" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="name">Nombre del Producto</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="description">Descripción</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="stock">Stock Inicial</label>
                        <input type="number" class="form-control" id="stock" name="stock" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="low_stock_level">Alerta Nivel de Stock</label>
                        <input type="number" class="form-control" id="low_stock_level" name="low_stock_level" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="list_price">Precio de Lista</label>
                        <input type="number" step="0.01" class="form-control" id="list_price" name="list_price" required>
                    </div>
                   
                </div>
                <div class="form-row">
                    
                    <div class="form-group col-md-6">
                        <label for="discount1">Descuento 1 (Ej: 10% debe escribir 10)</label>
                        <input type="number" step="0.01" class="form-control" id="discount1" name="discount1" placeholder="%">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="discount2">Descuento 2 (Ej: 4% debe escribir 4)</label>
                        <input type="number" step="0.01" class="form-control" id="discount2" name="discount2" placeholder="%">
                    </div>
                </div>

                <div class="form-row">
                    
                    <div class="form-group col-md-6">
                        <label for="discount3">Descuento 3 (Ej: 20% debe escribir 20)</label>
                        <input type="number" step="0.01" class="form-control" id="discount3" name="discount3" placeholder="%">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="discount4">Descuento 4 (Ej: 10% debe escribir 10)</label>
                        <input type="number" step="0.01" class="form-control" id="discount4" name="discount4" placeholder="%">
                    </div>
                </div>

                <div class="form-row">
                    
                    <div class="form-group col-md-6">
                        <label for="image">Imagen del Producto (opcional)</label>
                        <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block mt-3">Agregar Producto</button>
            </form>
        </div>
    
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
