<?php
// Ruta: control_stock/frontend/pages/profile.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../backend/controllers/authController.php';

$authController = new AuthController();

if (!$authController->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$message = "";
$error = "";

// Procesar actualización de contraseña propia
if (isset($_POST['update_profile'])) {
    $newPass = $_POST['new_password'];
    $confirmPass = $_POST['confirm_password'];

    if ($newPass === $confirmPass) {
        if ($authController->updatePassword($username, $newPass)) {
            $message = "Contraseña actualizada correctamente.";
        } else {
            $error = "Error al actualizar la contraseña.";
        }
    } else {
        $error = "Las contraseñas no coinciden.";
    }
}

// Procesar creación de nuevo usuario
if (isset($_POST['create_user'])) {
    $newUser = $_POST['new_username'];
    $newUserPass = $_POST['new_user_password'];

    if ($authController->createUser($newUser, $newUserPass)) {
        $message = "Usuario '$newUser' creado correctamente.";
    } else {
        $error = "Error al crear el usuario. Es posible que ya exista.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil | Julio Iturraspe</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/modern-system.css">
    <style>
        .main-layout { display: flex; min-height: 100vh; }
        .content-area { flex: 1; margin-left: 260px; padding: 2rem; background-color: #f8fafc; }
        .profile-card { background: white; border-radius: 1.25rem; padding: 2rem; border: none; box-shadow: var(--shadow-soft); max-width: 600px; margin-bottom: 2rem; }
        @media (max-width: 992px) { .content-area { margin-left: 0; } }
    </style>
</head>
<body class="bg-main">
    <div class="main-layout">
        <?php include_once '../components/Sidebar.php'; ?>
        
        <div class="content-area">
            <header class="mb-5">
                <h1 style="font-weight: 800; color: var(--text-main); letter-spacing: -0.025em;">Mi Perfil</h1>
                <p style="color: var(--text-muted); font-size: 1.1rem;">Gestioná tus credenciales y usuarios del sistema.</p>
            </header>

            <?php if ($message): ?>
                <div class="alert alert-success" style="border-radius: 0.75rem; border: none; background: #ecfdf5; color: #065f46;"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger" style="border-radius: 0.75rem; border: none; background: #fef2f2; color: #991b1b;"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="profile-card">
                        <h4 class="mb-4" style="font-weight: 700; color: var(--text-main);">Cambiar mi Contraseña</h4>
                        <form method="POST">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted uppercase">Usuario Actual</label>
                                <input type="text" class="modern-input" value="<?php echo $username; ?>" disabled>
                            </div>
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted uppercase">Nueva Contraseña</label>
                                <input type="password" name="new_password" class="modern-input" required>
                            </div>
                            <div class="form-group mb-4">
                                <label class="small font-weight-bold text-muted uppercase">Confirmar Nueva Contraseña</label>
                                <input type="password" name="confirm_password" class="modern-input" required>
                            </div>
                            <button type="submit" name="update_profile" class="modern-btn modern-btn-primary">Actualizar Contraseña</button>
                        </form>
                    </div>

                    <div class="profile-card">
                        <h4 class="mb-4" style="font-weight: 700; color: var(--text-main);">Crear Nuevo Usuario</h4>
                        <form method="POST">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted uppercase">Nombre de Usuario</label>
                                <input type="text" name="new_username" class="modern-input" required>
                            </div>
                            <div class="form-group mb-4">
                                <label class="small font-weight-bold text-muted uppercase">Contraseña para el Nuevo Usuario</label>
                                <input type="password" name="new_user_password" class="modern-input" required>
                            </div>
                            <button type="submit" name="create_user" class="modern-btn modern-btn-primary" style="background: #1e293b;">Crear Usuario</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
