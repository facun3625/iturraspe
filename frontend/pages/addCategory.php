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
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Categoría | Julio Iturraspe</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/modern-system.css">
    <style>
        .main-layout { display: flex; min-height: 100vh; }
        .content-area { flex: 1; margin-left: 260px; padding: 2rem; background-color: #f8fafc; }
        .form-card { background: white; border-radius: 1.5rem; padding: 2.5rem; border: none; box-shadow: var(--shadow-soft); max-width: 800px; margin: 0 auto; }
        @media (max-width: 992px) { .content-area { margin-left: 0; } }
    </style>
</head>
<body class="bg-main">
    <div class="main-layout">
        <?php include_once '../components/Sidebar.php'; ?>
        
        <div class="content-area">
            <div class="form-card">
                <div class="mb-5">
                    <h2 style="font-weight: 700; color: var(--text-main);">Agregar Nueva Categoría</h2>
                    <p style="color: var(--text-muted);">Define una nueva categoría para organizar tus productos y establecer márgenes.</p>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success auto-hide" style="border-radius: 0.75rem; border: none; background: #ecfdf5; color: #065f46;">Categoría agregada con éxito.</div>
                    <script>setTimeout(function() { window.location.href = 'categoryList.php'; }, 1500);</script>
                <?php elseif (isset($_GET['error'])): ?>
                    <div class="alert alert-danger" style="border-radius: 0.75rem; border: none; background: #fef2f2; color: #991b1b;">Error al agregar la categoría.</div>
                <?php endif; ?>

                <form action="../../backend/controllers/addCategoryHandler.php" method="post">
                    <div class="form-group mb-4">
                        <label class="modern-label">Nombre de la Categoría</label>
                        <input type="text" class="modern-input" name="name" placeholder="Ej: Cables, Iluminación, Herramientas" required>
                    </div>

                    <div class="form-group mb-4">
                        <label class="modern-label">Descripción (Opcional)</label>
                        <textarea class="modern-input" name="description" rows="3" placeholder="Breve descripción del tipo de productos en esta categoría"></textarea>
                    </div>

                    <div class="form-group mb-5">
                        <label class="modern-label">Porcentaje de Ganancia (%)</label>
                        <input type="number" class="modern-input" name="porcentaje" step="0.01" min="0" placeholder="Ej: 30.00" required>
                        <small class="text-muted mt-2 d-block">Este margen se aplicará automáticamente al calcular el precio final de los productos.</small>
                    </div>

                    <div class="d-flex gap-3">
                        <a href="categoryList.php" class="modern-btn" style="background: #f1f5f9; color: #475569; width: auto;">Volver al Listado</a>
                        <button type="submit" class="modern-btn modern-btn-primary" style="width: auto; flex: 1;">Crear Categoría</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
