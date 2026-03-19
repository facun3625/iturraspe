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
    <title>Agregar Producto | Julio Iturraspe</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/modern-system.css">
    <style>
        .main-layout { display: flex; min-height: 100vh; }
        .content-area { flex: 1; margin-left: 260px; padding: 2rem; background-color: #f8fafc; }
        .form-card { background: white; border-radius: 1.5rem; padding: 2.5rem; border: none; box-shadow: var(--shadow-soft); }
        .image-upload-zone {
            border: 2px dashed #e2e8f0; border-radius: 1rem; padding: 2rem; text-align: center; background: #f8fafc; cursor: pointer; transition: all 0.2s;
        }
        .image-upload-zone:hover { border-color: var(--primary); background: #eff6ff; }
        @media (max-width: 992px) { .content-area { margin-left: 0; } }
    </style>
</head>
<body class="bg-main">
    <div class="main-layout">
        <?php include_once '../components/Sidebar.php'; ?>
        
        <div class="content-area">
            <div class="form-card">
                <div class="mb-5">
                    <h2 style="font-weight: 700; color: var(--text-main);">Agregar Nuevo Producto</h2>
                    <p style="color: var(--text-muted);">Completa la ficha técnica para dar de alta un producto en el sistema.</p>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success" style="border-radius: 0.75rem; border: none; background: #ecfdf5; color: #065f46;">Producto agregado con éxito.</div>
                <?php elseif (isset($_GET['error'])): ?>
                    <div class="alert alert-danger" style="border-radius: 0.75rem; border: none; background: #fef2f2; color: #991b1b;">Error al agregar el producto.</div>
                <?php endif; ?>

                <form action="../../backend/controllers/addProductHandler.php" method="post" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="modern-label">Categoría</label>
                            <select class="modern-input" name="category_id" required>
                                <option value="">Seleccione una categoría</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="modern-label">ID / Código del Producto</label>
                            <input type="text" class="modern-input" name="cod" placeholder="Ej: PR-1001" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="modern-label">Nombre del Producto</label>
                            <input type="text" class="modern-input" name="name" placeholder="Ej: Cable unipolar 2.5mm" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="modern-label">Descripción (Opcional)</label>
                            <textarea class="modern-input" name="description" rows="3" placeholder="Detalles técnicos, marca, etc."></textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="modern-label">Precio de Lista</label>
                            <input type="number" step="0.01" class="modern-input" name="list_price" placeholder="0.00" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="modern-label">Stock Inicial</label>
                            <input type="number" class="modern-input" name="stock" placeholder="0" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="modern-label">Nivel Alerta Stock</label>
                            <input type="number" class="modern-input" name="low_stock_level" placeholder="5" required>
                        </div>
                    </div>

                    <div class="form-row mb-4">
                        <div class="col-12">
                            <div style="background: #f8fafc; padding: 1.5rem; border-radius: 1rem; border: 1px solid #e2e8f0;">
                                <p style="font-weight: 700; color: var(--text-main); margin-bottom: 1rem; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">Configuración de Descuentos (%)</p>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label class="modern-label" style="font-size: 0.75rem;">Desc. 1</label>
                                        <input type="number" step="0.01" class="modern-input" name="discount1" placeholder="0">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="modern-label" style="font-size: 0.75rem;">Desc. 2</label>
                                        <input type="number" step="0.01" class="modern-input" name="discount2" placeholder="0">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="modern-label" style="font-size: 0.75rem;">Desc. 3</label>
                                        <input type="number" step="0.01" class="modern-input" name="discount3" placeholder="0">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="modern-label" style="font-size: 0.75rem;">Desc. 4</label>
                                        <input type="number" step="0.01" class="modern-input" name="discount4" placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="modern-label">Imagen del Producto</label>
                        <div class="image-upload-zone" onclick="document.getElementById('image').click()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                            <p style="color: #64748b; margin: 0; font-size: 0.875rem;">Haz clic para subir una imagen</p>
                            <input type="file" id="image" name="image" accept="image/*" style="display: none;">
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <a href="productList.php" class="modern-btn" style="background: #f1f5f9; color: #475569; width: auto;">Ver Listado</a>
                        <button type="submit" class="modern-btn modern-btn-primary" style="width: auto; flex: 1;">Crear Producto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
