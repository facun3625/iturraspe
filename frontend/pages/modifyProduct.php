<?php
// Ruta: control_stock/frontend/pages/modifyProduct.php

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

require_once '../../backend/controllers/productController.php';

$productController = new ProductController();

if (!isset($_GET['id'])) {
    die("ID de producto no especificado.");
}

$product = $productController->getProductById($_GET['id']);

if (!$product) {
    die("Producto no encontrado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Producto</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include_once '../components/navbar.php'; ?>

    <div class="rounded-container-form-2">
        <h4 class="text-center mb-4">Modificar Producto</h4>

        <form action="../../backend/controllers/editProductHandler.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

    <div class="form-row">

    <div class="form-group col-md-6">
            <label for="category_id">Categoría</label>
            <select class="form-control" id="category_id" name="category_id" required>
                <?php
                require_once '../../backend/controllers/categoryController.php';
                $categoryController = new CategoryController();
                $categories = $categoryController->getCategories();

                foreach ($categories as $category) {
                    $selected = $category['id'] == $product['category_id'] ? 'selected' : '';
                    echo "<option value='{$category['id']}' $selected>{$category['name']}</option>";
                }
                ?>
            </select>
        </div>
        
        <div class="form-group col-md-6">
            <label for="cod">ID</label>
            <input type="text" class="form-control" id="cod" name="cod" value="<?php echo $product['cod']; ?>" required>
        </div>
    </div>
    
    <div class="form-row">
        
    <div class="form-group col-md-6">
            <label for="name">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $product['name']; ?>" required>
        </div>
        <div class="form-group col-md-6">
            <label for="list_price">Precio de Lista</label>
            <input type="number" step="0.01" class="form-control" id="list_price" name="list_price" value="<?php echo $product['list_price']; ?>" required>
        </div>
        
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="stock">Stock</label>
            <input type="number" class="form-control" id="stock" name="stock" value="<?php echo $product['stock']; ?>" required>
        </div>
        <div class="form-group col-md-6">
            <label for="low_stock_level">Alerta Nivel de Stock</label>
            <input type="number" class="form-control" id="low_stock_level" name="low_stock_level" value="<?php echo $product['low_stock_level']; ?>" required>
        </div>
    </div>


    <div class="form-row">
    <div class="form-group col-md-6">
            <label for="discount1">Descuento 1 (%)</label>
            <input type="number" step="0.01" class="form-control" id="discount1" name="discount1" value="<?php echo 100 - ($product['discount1'] * 100); ?>">
        </div>
        <div class="form-group col-md-6">
            <label for="discount2">Descuento 2 (%)</label>
            <input type="number" step="0.01" class="form-control" id="discount2" name="discount2" value="<?php echo 100 - ($product['discount2'] * 100); ?>">
        </div>
        
    </div>

    <div class="form-row">
    <div class="form-group col-md-6">
            <label for="discount3">Descuento 3 (%)</label>
            <input type="number" step="0.01" class="form-control" id="discount3" name="discount3" value="<?php echo 100 - ($product['discount3'] * 100); ?>">
        </div>
        <div class="form-group col-md-6">
            <label for="discount4">Descuento 4 (%)</label>
            <input type="number" step="0.01" class="form-control" id="discount4" name="discount4" value="<?php echo 100 - ($product['discount4'] * 100); ?>">
        </div>
        <div class="form-group col-md-6">
            <label>Imagen actual</label>
            <?php if (!empty($product['image_url'])): ?>
                <div class="mb-3">
                    <img src="../<?php echo $product['image_url']; ?>" alt="Imagen del producto" class="img-thumbnail" style="max-width: 200px;">
                </div>
            <?php else: ?>
                <p>No hay imagen para este producto.</p>
            <?php endif; ?>
            <label for="image">Subir nueva imagen (opcional)</label>
            <input type="file" class="form-control-file" id="image" name="image">
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
</form>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
