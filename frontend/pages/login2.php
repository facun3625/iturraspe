<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-secondary d-flex justify-content-center align-items-center p-5" style="height: 100vh;">
    <div class="card p-4" style="width: 100%; max-width: 400px;">
        <h4 class="text-center text-primary">Julio Iturraspe</h4>
        <h5 class="text-center">Iniciar Sesión</h5>
        
        <form action="../../backend/controllers/loginHandler.php" method="post">
            <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <!-- Mensaje de error estilizado -->
            <div class="alert alert-danger text-center" role="alert">
                Usuario o contraseña incorrectos
            </div>

            <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
