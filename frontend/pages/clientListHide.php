<?php
// Ruta: control_stock/frontend/pages/clientList.php

// Mostrar errores en la pantalla
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

require_once '../../backend/controllers/clientController.php';
$clientController = new ClientController();
$clients = $clientController->getClientsWithDebtHide(); // Este método incluirá el cálculo de deuda

if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo "<div class='alert alert-success text-center'>Cliente añadido correctamente.</div>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Clientes</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="style.css">
</head>
</head>
<body>

    <?php include_once '../components/navbar.php'; ?>

    <div class="rounded-container-table ">
        <h4 class="text-center mb-4">Listado de Clientes eliminados</h4>
        <table class="table table-bordered table-striped mt-3" id="clientTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Cantidad de Compras</th>
                    <th>Deuda</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $client): ?>
                    <tr>
                        <td><?php echo $client['id']; ?></td>
                        <!-- El nombre del cliente ahora es un enlace a la vista de ventas del cliente -->
                        <td>
                            <a href="clientSales.php?client_id=<?php echo $client['id']; ?>">
                                <?php echo $client['name']; ?>
                            </a>
                        </td>
                        <td><?php echo $client['purchase_count']; ?></td> <!-- Este campo muestra el número de compras -->
                        <td>$<?php echo number_format($client['debt'], 2); ?></td> <!-- Muestra el total de deuda -->
                        <td>
                            
                            
                            <!-- Botón para Eliminar -->
                            <a href="../../backend/controllers/activeClientHandler.php?id=<?php echo $client['id']; ?>" class="btn btn-primary btn-sm" onclick="return confirm('¿Estás seguro de Activar este cliente?')">Activar</a>
                            
                            <!-- Botón para Iniciar Venta -->
                            <!-- <a href="sales.php?client_id=<?php echo $client['id']; ?>" class="btn btn-secondary btn-sm">Iniciar Venta</a> -->
                        </td>
                    </tr>

                    
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="addClient.php" class="btn btn-success">Agregar Nuevo Cliente</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#clientTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
