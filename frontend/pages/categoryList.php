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

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías | Julio Iturraspe</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../assets/css/modern-system.css">
    <style>
        .main-layout { display: flex; min-height: 100vh; }
        .content-area { flex: 1; margin-left: 260px; padding: 2rem; background-color: #f8fafc; }
        .table-card { background: white; border-radius: 1rem; padding: 2rem; border: none; box-shadow: var(--shadow-soft); }
        table.dataTable { border-collapse: separate !important; border-spacing: 0 0.5rem !important; }
        table.dataTable tbody tr { background-color: #fff !important; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border-radius: 0.5rem; transition: transform 0.2s; }
        table.dataTable tbody tr:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.07); }
        table.dataTable tbody td { border: none !important; padding: 1rem !important; vertical-align: middle !important; }
        table.dataTable thead th { border-bottom: 2px solid #f1f5f9 !important; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; }
        .count-badge { background: #eff6ff; color: #1e40af; padding: 0.4rem 0.6rem; border-radius: 0.5rem; font-weight: 700; font-size: 0.875rem; }
        .porcentaje-badge { background: #f0fdf4; color: #166534; padding: 0.4rem 0.6rem; border-radius: 0.5rem; font-weight: 700; }
        .action-btn { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 0.5rem; transition: all 0.2s; border: none; }
        @media (max-width: 992px) { .content-area { margin-left: 0; } }
    </style>
</head>
<body class="bg-main">
    <div class="main-layout">
        <?php include_once '../components/Sidebar.php'; ?>
        
        <div class="content-area">
            <div class="table-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 style="font-weight: 700; color: var(--text-main); margin: 0;">Categorías de Productos</h2>
                        <p style="color: var(--text-muted); margin: 0;">Gestiona las agrupaciones y márgenes de ganancia.</p>
                    </div>
                    <a href="addCategory.php" class="modern-btn modern-btn-primary" style="width: auto;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                        Nueva Categoría
                    </a>
                </div>

                <!-- Mensajes -->
                <?php if (isset($_GET['edit_success'])): ?>
                    <div class="alert alert-success" style="border-radius:0.75rem; border:none; background:#ecfdf5; color:#065f46;">Categoría editada con éxito.</div>
                <?php elseif (isset($_GET['delete_success'])): ?>
                    <div class="alert alert-success auto-hide" style="border-radius:0.75rem; border:none; background:#ecfdf5; color:#065f46;">Categoría eliminada con éxito.</div>
                <?php elseif (isset($error) || isset($_GET['edit_error']) || isset($_GET['delete_error'])): ?>
                    <div class="alert alert-danger" style="border-radius:0.75rem; border:none; background:#fef2f2; color:#991b1b;">Error en la operación.</div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table id="categoryTable" class="table">
                        <thead>
                            <tr>
                                <th>Cod.</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th class="text-center">Productos</th>
                                <th class="text-center">Margen (%)</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td style="font-weight: 600; color: var(--text-muted);"><?php echo $category['id']; ?></td>
                                    <td style="font-weight: 700; color: var(--text-main);"><?php echo $category['name']; ?></td>
                                    <td style="color: #64748b; font-size: 0.875rem; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo $category['description']; ?></td>
                                    <td class="text-center"><span class="count-badge"><?php echo $category['product_count']; ?></span></td>
                                    <td class="text-center"><span class="porcentaje-badge"><?php echo number_format($category['porcentaje'], 2); ?>%</span></td>
                                    <td class="text-right">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button class="action-btn editBtn" 
                                                    data-id="<?php echo $category['id']; ?>" 
                                                    data-name="<?php echo $category['name']; ?>" 
                                                    data-description="<?php echo $category['description']; ?>" 
                                                    data-porcentaje="<?php echo $category['porcentaje']; ?>"
                                                    style="background: #eff6ff; color: #1e40af;" title="Editar">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                            </button>
                                            <a href="../../backend/controllers/deleteCategoryHandler.php?id=<?php echo $category['id']; ?>" 
                                               class="action-btn deleteBtn" 
                                               style="background: #fef2f2; color: #991b1b;" title="Eliminar"
                                               onclick="return confirm('¿Seguro que deseas eliminar esta categoría?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Edición Moderno -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" style="font-weight: 700; color: var(--text-main);">Editar Categoría</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="../../backend/controllers/editCategoryHandler.php" method="post">
                    <div class="modal-body py-4">
                        <input type="hidden" name="id" id="editCategoryId">
                        <div class="form-group">
                            <label class="modern-label">Nombre</label>
                            <input type="text" class="modern-input" id="editName" name="name" required>
                        </div>
                        <div class="form-group">
                            <label class="modern-label">Descripción</label>
                            <textarea class="modern-input" id="editDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="form-group mb-0">
                            <label class="modern-label">Porcentaje de Ganancia (%)</label>
                            <input type="number" class="modern-input" id="editPorcentaje" name="porcentaje" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="modern-btn" style="background: #f1f5f9; color: #475569; width: auto;" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="modern-btn modern-btn-primary" style="width: auto; padding-left: 2rem; padding-right: 2rem;">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#categoryTable').DataTable({
                "language": { "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json" },
                "dom": '<"d-flex justify-content-between align-items-center mb-4"lf>rtip'
            });

            setTimeout(function() { $('.auto-hide').fadeOut('slow'); }, 2000);

            $('.editBtn').on('click', function() {
                $('#editCategoryId').val($(this).data('id'));
                $('#editName').val($(this).data('name'));
                $('#editDescription').val($(this).data('description'));
                $('#editPorcentaje').val($(this).data('porcentaje'));
                $('#editModal').modal('show');
            });
        });
    </script>
</body>
</body>
</html>
