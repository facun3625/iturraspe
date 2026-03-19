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
$clients = $clientController->getClientsWithDebtOnly(); // Este método incluirá el cálculo de deuda

// Calcular el total de deuda
$totalDebt = array_reduce($clients, function ($sum, $client) {
    return $sum + $client['debt'];
}, 0);

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
<body>

    <?php include_once '../components/navbar.php'; ?>

    <div class="rounded-container-table">
        <h4 class="text-center">Listado de Clientes Con Deudas</h4>

        <!-- Botón para generar PDF -->
        <div class="text-right mb-3">
            <button id="printPdfBtn" class="btn btn-danger btn-sm">Imprimir en PDF</button>
        </div>

        <!-- Mostrar el total de deuda -->
        <div class="alert alert-info text-left">
            <strong>Total de Deuda:</strong> $<?php echo number_format($totalDebt, 2); ?>
        </div>

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
                        <td>
                            <a href="clientSales.php?client_id=<?php echo $client['id']; ?>">
                                <?php echo $client['name']; ?>
                            </a>
                        </td>
                        <td><?php echo $client['purchase_count']; ?></td>
                        <td>$<?php echo number_format($client['debt'], 2); ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editClientModal<?php echo $client['id']; ?>">Editar</button>
                            <a href="../../backend/controllers/deleteClientHandler.php?id=<?php echo $client['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este cliente?')">Eliminar</a>
                            <a href="sales.php?client_id=<?php echo $client['id']; ?>" class="btn btn-success btn-sm">Iniciar Venta</a>
                        </td>
                    </tr>

                    <!-- Modal para Editar Cliente -->
                    <div class="modal fade" id="editClientModal<?php echo $client['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editClientModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Editar Cliente</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="../../backend/controllers/updateClientHandler.php" method="post">
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?php echo $client['id']; ?>">
                                        <div class="form-group">
                                            <label for="name">Nombre</label>
                                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $client['name']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="phone">Teléfono</label>
                                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $client['phone']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $client['email']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="cuit">CUIT</label>
                                            <input type="text" class="form-control" id="cuit" name="cuit" value="<?php echo $client['cuit']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="address">Dirección</label>
                                            <input type="text" class="form-control" id="address" name="address" value="<?php echo $client['address']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Fin del Modal -->

                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="addClient.php" class="btn btn-success">Agregar Nuevo Cliente</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- DataTables -->
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

    <!-- Incluir jsPDF y jsPDF-AutoTable -->
    <script src="https://cdn.jsdelivr.net/npm/jspdf/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable/dist/jspdf.plugin.autotable.min.js"></script>
    <script>
        document.getElementById('printPdfBtn').addEventListener('click', function() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Título del PDF
            doc.text("Listado de Clientes Con Deudas", 14, 10);

            // Definir las columnas (excluyendo la columna de "Acciones")
            const tableColumns = ['ID', 'Nombre', 'Cantidad de Compras', 'Deuda'];

            // Preparar las filas a partir de la variable PHP $clients
            const tableRows = <?php echo json_encode(array_map(function($client) {
                return [
                    $client['id'],
                    $client['name'],
                    $client['purchase_count'],
                    '$' . number_format($client['debt'], 2)
                ];
            }, $clients)); ?>;

            // Generar la tabla en el PDF
            doc.autoTable({
                startY: 20,
                head: [tableColumns],
                body: tableRows,
                theme: 'grid'
            });

            // Guardar el PDF
            doc.save('listado_clientes.pdf');
        });
    </script>
</body>
</html>
