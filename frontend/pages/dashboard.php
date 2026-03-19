<?php
session_start();



?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Stock - Panel de Control</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navbar con Logo y Menú -->
    <?php include_once '../components/navbar.php'; ?>

    <!-- Contenedor Principal con bordes redondeados -->
    <div class="container mt-5">
        <div class="rounded-container p-4">
            <h1 class="text-center">Bienvenido, <?php echo $_SESSION['username']; ?>!</h1>
            <p class="text-center">Este es tu panel de control. Desde aquí, puedes gestionar las diferentes áreas de la aplicación.</p>

            <div class="row mt-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Categorías</h5>
                            <p class="card-text">Gestiona las categorías de productos.</p>
                            <a href="addCategory.php" class="btn btn-primary ">Agregar Categoría</a>
                            <a href="categoryList.php" class="btn btn-primary mt-1">Ir a Categorías</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Productos</h5>
                            <p class="card-text">Gestiona los productos y su inventario.</p>
                            <a href="addProduct.php" class="btn btn-primary ">Agregar Producto</a>
                            <a href="uploadPrices.php" class="btn btn-primary mt-1">Actualizar Precios</a>
                            <a href="productListLowStock.php" class="btn btn-primary mt-1">Alertas de Stock</a>
                            <a href="productList.php" class="btn btn-primary mt-1">Ver Productos</a>

                            <a href="priceList.php" class="btn btn-primary mt-1" target="_blank">Lista de Precios</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Clientes</h5>
                            <p class="card-text">Gestiona los datos de tus clientes.</p>
                            <a href="addClient.php" class="btn btn-primary">Agregar Cliente</a>
                            <a href="clientList.php" class="btn btn-primary mt-1">Ir a Clientes</a>
                            <a href="clientListDeb.php" class="btn btn-primary mt-1">Clientes con Deudas</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Ventas</h5>
                            <p class="card-text">Registra nuevas ventas y consulta el historial.</p>
                            <a href="clientList.php" class="btn btn-primary">Realizar una Venta</a>
                            <a href="salesList.php" class="btn btn-primary mt-1">Todas las Ventas</a>
                            <a href="payments.php" class="btn btn-primary mt-1">Pagos Recibidos</a>
                            <a href="productReport.php" class="btn btn-primary mt-1">Reporte de Ventas</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Estadísticas</h5>
                            
                            <a href="statistics.php" class="btn btn-primary mt-1">Ver Estadísticas</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

   
</body>
</html>
