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
            background: #f1f5f9;
            padding: 1.5rem;
        }
        .login-card {
            background: white;
            border-radius: 2rem;
            padding: 3rem;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(226, 232, 240, 0.8);
            text-align: center;
        }
        .brand-logo-large {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2.5rem;
            color: white;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        }
        .login-title {
            font-size: 2rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.75rem;
            letter-spacing: -0.025em;
        }
        .login-subtitle {
            font-size: 1rem;
            color: #64748b;
            margin-bottom: 3rem;
        }
        .input-group-custom {
            text-align: left;
            margin-bottom: 1.5rem;
        }
        .input-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
            padding-left: 0.25rem;
        }
        .footer-text {
            margin-top: 3rem;
            color: #94a3b8;
            font-size: 0.75rem;
            font-weight: 500;
        }
    </style>
</head>
<body class="bg-main">
    <div class="login-container">
        <div class="login-card">
            <div class="brand-logo-large">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
            </div>
            
            <h1 class="login-title">Bienvenido</h1>
            <p class="login-subtitle">Ingresá tus credenciales para acceder</p>

            <form action="../../backend/controllers/loginHandler.php" method="post">
                <div class="input-group-custom">
                    <label class="input-label">Usuario</label>
                    <input type="text" name="username" class="modern-input" placeholder="Nombre de usuario" required autofocus>
                </div>
                
                <div class="input-group-custom" style="margin-bottom: 2.5rem;">
                    <label class="input-label">Contraseña</label>
                    <input type="password" name="password" class="modern-input" placeholder="••••••••" required>
                </div>

                <button type="submit" class="modern-btn modern-btn-primary" style="padding: 1rem; font-size: 1rem;">
                    Iniciar Sesión
                </button>
            </form>

            <p class="footer-text">
                &copy; <?php echo date('Y'); ?> Distribuidora Julio Iturraspe
            </p>
        </div>
    </div>
</body>
</html>
