<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Julio Iturraspe</title>
    <link rel="stylesheet" href="../assets/css/modern-system.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            padding: 1rem;
        }
        .brand-logo {
            width: 60px;
            height: 60px;
            background: var(--primary);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        }
    </style>
</head>
<body class="login-bg">
    <div class="login-container">
        <div class="glass-card" style="width: 100%; max-width: 400px;">
            <div class="brand-logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
            </div>
            <h2 style="text-align: center; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-main);">Bienvenido</h2>
            <p style="text-align: center; color: var(--text-muted); margin-bottom: 2rem; font-size: 0.875rem;">Ingresa tus credenciales para continuar</p>
            
            <form action="../../backend/controllers/loginHandler.php" method="post">
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">Usuario</label>
                    <input type="text" name="username" class="modern-input" placeholder="Tu nombre de usuario" required autofocus>
                </div>
                <div style="margin-bottom: 2rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">Contraseña</label>
                    <input type="password" name="password" class="modern-input" placeholder="••••••••" required>
                </div>
                <button type="submit" class="modern-btn modern-btn-primary">
                    Iniciar Sesión
                </button>
            </form>
            
            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e2e8f0; text-align: center;">
                <p style="font-size: 0.75rem; color: var(--text-muted);">&copy; <?php echo date('Y'); ?> Distribuidora Julio Iturraspe</p>
            </div>
        </div>
    </div>
</body>
</html>
