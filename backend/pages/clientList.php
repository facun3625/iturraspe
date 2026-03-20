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
$clients = $clientController->getClientsWithDebt(); // Este método incluirá el cálculo de deuda

// Calcular el total de deuda
$totalDebt = array_reduce($clients, function ($sum, $client) {
    return $sum + ($client['debt'] > 0 ? $client['debt'] : 0);
}, 0);

if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo "<div class='alert alert-success text-center'>Cliente añadido correctamente.</div>";
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes | Julio Iturraspe</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../assets/css/modern-system.css">
    <style>
        .main-layout { display: flex; min-height: 100vh; }
        .content-area { flex: 1; margin-left: 260px; padding: 2rem; background-color: #f8fafc; }
        .table-card { background: white; border-radius: 1rem; padding: 2rem; border: none; box-shadow: var(--shadow-soft); }
        .debt-card { background: #eff6ff; border-left: 4px solid var(--primary); padding: 1.5rem; border-radius: 0.75rem; margin-bottom: 2rem; }
        table.dataTable { border-collapse: separate !important; border-spacing: 0 0.5rem !important; }
        table.dataTable tbody tr { background-color: #fff !important; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border-radius: 0.5rem; transition: transform 0.2s; }
        table.dataTable tbody tr:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.07); }
        table.dataTable tbody td { border: none !important; padding: 1rem !important; vertical-align: middle !important; }
        table.dataTable thead th { border-bottom: 2px solid #f1f5f9 !important; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; }
        .debt-badge { background: #fee2e2; color: #991b1b; padding: 0.4rem 0.6rem; border-radius: 0.5rem; font-weight: 700; }
        .purchase-badge { background: #f1f5f9; color: #475569; padding: 0.4rem 0.6rem; border-radius: 0.5rem; font-weight: 600; }
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
                        <h2 style="font-weight: 700; color: var(--text-main); margin: 0;">Cartera de Clientes</h2>
                        <p style="color: var(--text-muted); margin: 0;">Administra la información y estados de cuenta de tus clientes.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button id="printPdfBtn" class="modern-btn" style="background: #ef4444; width: auto; color: white;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M12 18v-6"/><path d="m9 15 3 3 3-3"/></svg>
                            PDF
                        </button>
                        <a href="addClient.php" class="modern-btn modern-btn-primary" style="width: auto;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                            Nuevo Cliente
                        </a>
                    </div>
                </div>

                <div class="debt-card d-flex justify-content-between align-items-center">
                    <div>
                        <span style="display: block; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #1e40af; font-weight: 700; margin-bottom: 0.25rem;">Deuda Total Acumulada</span>
                        <h3 style="margin: 0; font-weight: 800; color: #1e3a8a;">$<?php echo number_format($totalDebt, 2); ?></h3>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#1e40af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="m17 5-5-3-5 3"/><path d="m17 19-5 3-5-3"/><rect width="14" height="20" x="5" y="2" rx="2"/></svg>
                </div>

                <div class="table-responsive">
                    <table id="clientTable" class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre Completo</th>
                                <th class="text-center">Compras</th>
                                <th class="text-center">Saldo</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients as $client): ?>
                                <tr>
                                    <td style="font-weight: 600; color: var(--text-muted);"><?php echo $client['id']; ?></td>
                                    <td>
                                        <a href="clientSales.php?client_id=<?php echo $client['id']; ?>" style="font-weight: 700; color: var(--text-main); text-decoration: none;">
                                            <?php echo $client['name']; ?>
                                        </a>
                                    </td>
                                    <td class="text-center"><span class="purchase-badge"><?php echo $client['purchase_count']; ?></span></td>
                                    <td class="text-center">
                                        <?php if ($client['debt'] > 0): ?>
                                            <span class="debt-badge">$<?php echo number_format($client['debt'], 2); ?></span>
                                        <?php else: ?>
                                            <span style="color: #10b981; font-weight: 700;">Al día</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="sales.php?client_id=<?php echo $client['id']; ?>" class="action-btn" style="background: #dcfce7; color: #166534;" title="Iniciar Venta">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m5 11 4-7"/><path d="m19 11-4-7"/><path d="M2 11h20"/><path d="m3.5 11 1.6 7.4a2 2 0 0 0 2 1.6h9.8a2 2 0 0 0 2-1.6l1.7-7.4"/><path d="m9 11 1 9"/><path d="m15 11-1 9"/></svg>
                                            </a>
                                            <button class="action-btn" style="background: #eff6ff; color: #1e40af;" data-toggle="modal" data-target="#editClientModal<?php echo $client['id']; ?>" title="Editar">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                            </button>
                                            <a href="../../backend/controllers/deleteClientHandler.php?id=<?php echo $client['id']; ?>" class="action-btn" style="background: #fef2f2; color: #991b1b;" onclick="return confirm('¿Estás seguro de eliminar este cliente?')" title="Eliminar">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal de Edición Moderno -->
                                <div class="modal fade" id="editClientModal<?php echo $client['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title" style="font-weight: 700; color: var(--text-main);">Editar Cliente</h5>
                                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                            </div>
                                            <form action="../../backend/controllers/updateClientHandler.php" method="post">
                                                <div class="modal-body py-4">
                                                    <input type="hidden" name="id" value="<?php echo $client['id']; ?>">
                                                    <div class="form-group">
                                                        <label class="modern-label">Nombre Completo</label>
                                                        <input type="text" class="modern-input" name="name" value="<?php echo $client['name']; ?>" required>
                                                    </div>
                                                    <div class="form-row">
                                                        <div class="form-group col-md-6">
                                                            <label class="modern-label">Teléfono</label>
                                                            <input type="text" class="modern-input" name="phone" value="<?php echo $client['phone']; ?>">
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label class="modern-label">CUIT</label>
                                                            <input type="text" class="modern-input" name="cuit" value="<?php echo $client['cuit']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="modern-label">Email</label>
                                                        <input type="email" class="modern-input" name="email" value="<?php echo $client['email']; ?>">
                                                    </div>
                                                    <div class="form-group mb-0">
                                                        <label class="modern-label">Dirección</label>
                                                        <input type="text" class="modern-input" name="address" value="<?php echo $client['address']; ?>">
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
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#clientTable').DataTable({
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No se encontraron resultados",
                    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sSearch": "Buscar:",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "sProcessing": "Procesando...",
                },
                "dom": '<"d-flex justify-content-between align-items-center mb-4"lf>rtip'
            });

            document.getElementById('printPdfBtn').addEventListener('click', function() {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();
                doc.autoTable({ html: '#clientTable', columns: [0, 1, 2, 3] });
                doc.save('clientes_iturraspe.pdf');
            });
        });
    </script>
</body>
</body>
</html>
