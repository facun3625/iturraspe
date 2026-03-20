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
    <title>Nuevo Cliente | Julio Iturraspe</title>
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
                <div class="mb-5 text-center">
                    <h2 style="font-weight: 700; color: var(--text-main);">Alta de Nuevo Cliente</h2>
                    <p style="color: var(--text-muted);">Completa los datos para registrar un nuevo cliente en el sistema.</p>
                </div>

                <form action="../../backend/controllers/addClientHandler.php" method="post">
                    <div class="form-group mb-4">
                        <label class="modern-label">Nombre Completo / Razón Social</label>
                        <input type="text" class="modern-input" name="name" placeholder="Ej: Juan Pérez o Distribuidora S.A." required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6 mb-4">
                            <label class="modern-label">Teléfono</label>
                            <input type="text" class="modern-input" name="phone" placeholder="Ej: 11 1234-5678">
                        </div>
                        <div class="form-group col-md-6 mb-4">
                            <label class="modern-label">CUIT / CUIL</label>
                            <input type="text" class="modern-input" name="cuit" placeholder="Ej: 20-12345678-9">
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="modern-label">Correo Electrónico</label>
                        <input type="email" class="modern-input" name="email" placeholder="ejemplo@correo.com">
                    </div>

                    <div class="form-group mb-5">
                        <label class="modern-label">Dirección Particular / Comercial</label>
                        <input type="text" class="modern-input" name="address" placeholder="Ej: Av. Rivadavia 1234, CABA">
                    </div>

                    <div class="d-flex gap-3">
                        <a href="clientList.php" class="modern-btn" style="background: #f1f5f9; color: #475569; width: auto;">Volver al Listado</a>
                        <button type="submit" class="modern-btn modern-btn-primary" style="width: auto; flex: 1;">Registrar Cliente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
