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
    <title>Lista de Categorías</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include_once '../components/navbar.php'; ?>
    <div class="rounded-container-table">
        <div>
            <h4 class="text-center pb-4">Lista de Categorías</h4>

            <!-- Mensajes de éxito o error -->
            <?php if (isset($_GET['edit_success'])): ?>
                <div class="alert alert-success">Categoría editada con éxito.</div>
            <?php elseif (isset($_GET['edit_error'])): ?>
                <div class="alert alert-danger">Error al editar la categoría.</div>
            <?php elseif (isset($_GET['delete_success'])): ?>
                <div class="alert alert-success auto-hide">Categoría eliminada con éxito.</div>
            <?php elseif (isset($_GET['delete_error'])): ?>
                <div class="alert alert-danger">Error al eliminar la categoría.</div>
            <?php elseif (isset($error)): ?>
                <div class="alert alert-danger text-center"><?php echo $error; ?></div>
            <?php endif; ?>

            <table id="categoryTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Cantidad de Productos</th>
                        <th>Porcentaje de Ganancia (%)</th> <!-- Nueva columna -->
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo $category['id']; ?></td>
                            <td><?php echo $category['name']; ?></td>
                            <td><?php echo $category['description']; ?></td>
                            <td><?php echo $category['product_count']; ?></td>
                            <td><?php echo number_format($category['porcentaje'], 2); ?></td> <!-- Mostrar porcentaje -->
                            <td>
                                <button class="btn btn-secondary btn-sm editBtn" data-id="<?php echo $category['id']; ?>" data-name="<?php echo $category['name']; ?>" data-description="<?php echo $category['description']; ?>" data-porcentaje="<?php echo $category['porcentaje']; ?>">Editar</button>
                                <a href="../../backend/controllers/deleteCategoryHandler.php?id=<?php echo $category['id']; ?>" class="btn btn-danger btn-sm deleteBtn">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal para Editar Categoría -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Editar Categoría</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="../../backend/controllers/editCategoryHandler.php" method="post">
                        <div class="modal-body">
                            <input type="hidden" name="id" id="editCategoryId">
                            <div class="form-group">
                                <label for="editName">Nombre</label>
                                <input type="text" class="form-control" id="editName" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="editDescription">Descripción</label>
                                <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="editPorcentaje">Porcentaje de Ganancia (%)</label>
                                <input type="number" class="form-control" id="editPorcentaje" name="porcentaje" step="0.01" min="0" required> <!-- Nuevo campo -->
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
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#categoryTable').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"
                }
            });

            // Ocultar el mensaje de éxito de eliminación después de 1.5 segundos
            setTimeout(function() {
                $('.auto-hide').fadeOut('slow');
            }, 1500);

            // Activar el modal de edición y rellenar datos
            $('.editBtn').on('click', function() {
                $('#editCategoryId').val($(this).data('id'));
                $('#editName').val($(this).data('name'));
                $('#editDescription').val($(this).data('description'));
                $('#editPorcentaje').val($(this).data('porcentaje')); // Llenar el porcentaje en el modal
                $('#editModal').modal('show'); // Mostrar el modal
            });
        });
    </script>
</body>
</html>
