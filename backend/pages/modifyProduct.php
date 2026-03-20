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
    <title>Modificar Producto | Julio Iturraspe</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/modern-system.css">
    <style>
        .main-layout { display: flex; min-height: 100vh; }
        .content-area { flex: 1; margin-left: 260px; padding: 2rem; background-color: #f8fafc; }
        .form-card { background: white; border-radius: 1.5rem; padding: 2.5rem; border: none; box-shadow: var(--shadow-soft); }
        .image-preview-container {
            border: 2px dashed #e2e8f0; border-radius: 1rem; padding: 2rem; text-align: center; background: #f8fafc; margin-bottom: 2rem;
        }
        .image-preview-container img {
            border-radius: 0.75rem; box-shadow: var(--shadow-soft); margin-bottom: 1rem;
        }
        @media (max-width: 992px) { .content-area { margin-left: 0; } }
    </style>
</head>
<body class="bg-main">
    <div class="main-layout">
        <?php include_once '../components/Sidebar.php'; ?>
        
        <div class="content-area">
            <div class="form-card">
                <div class="mb-5">
                    <h2 style="font-weight: 700; color: var(--text-main);">Modificar Producto</h2>
                    <p style="color: var(--text-muted);">Actualiza la información técnica y comercial del producto.</p>
                </div>

                <form action="../../backend/controllers/editProductHandler.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="modern-label">Categoría</label>
                            <select class="modern-input" name="category_id" required>
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
                            <label class="modern-label">ID / Código</label>
                            <input type="text" class="modern-input" name="cod" value="<?php echo $product['cod']; ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="modern-label">Nombre del Producto</label>
                            <input type="text" class="modern-input" name="name" value="<?php echo $product['name']; ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="modern-label">Precio de Lista</label>
                            <input type="number" step="0.01" class="modern-input" name="list_price" value="<?php echo $product['list_price']; ?>" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="modern-label">Stock Actual</label>
                            <input type="number" class="modern-input" name="stock" value="<?php echo $product['stock']; ?>" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="modern-label">Nivel Alerta Stock</label>
                            <input type="number" class="modern-input" name="low_stock_level" value="<?php echo $product['low_stock_level']; ?>" required>
                        </div>
                    </div>

                    <div class="form-row mb-4">
                        <div class="col-12">
                            <div style="background: #f8fafc; padding: 1.5rem; border-radius: 1rem; border: 1px solid #e2e8f0;">
                                <p style="font-weight: 700; color: var(--text-main); margin-bottom: 1rem; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">Descuentos (%)</p>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label class="modern-label" style="font-size: 0.75rem;">Desc. 1</label>
                                        <input type="number" step="0.01" class="modern-input" name="discount1" value="<?php echo 100 - ($product['discount1'] * 100); ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="modern-label" style="font-size: 0.75rem;">Desc. 2</label>
                                        <input type="number" step="0.01" class="modern-input" name="discount2" value="<?php echo 100 - ($product['discount2'] * 100); ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="modern-label" style="font-size: 0.75rem;">Desc. 3</label>
                                        <input type="number" step="0.01" class="modern-input" name="discount3" value="<?php echo 100 - ($product['discount3'] * 100); ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="modern-label" style="font-size: 0.75rem;">Desc. 4</label>
                                        <input type="number" step="0.01" class="modern-input" name="discount4" value="<?php echo 100 - ($product['discount4'] * 100); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="image-preview-container">
                        <label class="modern-label" style="display: block; margin-bottom: 1rem;">Imagen del Producto</label>
                        <?php if (!empty($product['image_url'])): ?>
                            <img src="../<?php echo $product['image_url']; ?>" alt="Imagen del producto" style="max-width: 180px;">
                        <?php else: ?>
                            <div style="padding: 2rem; color: var(--text-muted);">Sin imagen configurada</div>
                        <?php endif; ?>
                        <div class="mt-3">
                            <input type="file" class="form-control-file" id="image" name="image" style="display: none;">
                            <button type="button" class="modern-btn" style="background: white; border: 1px solid #e2e8f0; width: auto; display: inline-flex;" onclick="document.getElementById('image').click()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7"/><line x1="16" x2="22" y1="5" y2="5"/><line x1="19" x2="19" y1="2" y2="8"/></svg>
                                Cambiar Imagen
                            </button>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <a href="productList.php" class="modern-btn" style="background: #f1f5f9; color: #475569; width: auto;">Cancelar</a>
                        <button type="submit" class="modern-btn modern-btn-primary" style="width: auto; flex: 1;">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
