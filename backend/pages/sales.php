<?php
// Ruta: control_stock/frontend/pages/sales.php

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

// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../backend/controllers/clientController.php';
require_once '../../backend/controllers/productController.php';

$clientController = new ClientController();
$productController = new ProductController();

$client_id = $_GET['client_id'];
$client = $clientController->getClientById($client_id); // Función que obtiene los datos del cliente
$products = $productController->getProductsWithCalculatedPrice(); // Cambiar para obtener productos con precios calculados

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Venta | Julio Iturraspe</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../assets/css/modern-system.css">
    <style>
        .main-layout { display: flex; min-height: 100vh; }
        .content-area { flex: 1; margin-left: 260px; padding: 2rem; background-color: #f8fafc; }
        .split-layout { display: grid; grid-template-columns: 1fr 400px; gap: 2rem; }
        .card-container { background: white; border-radius: 1.25rem; padding: 1.5rem; border: none; box-shadow: var(--shadow-soft); }
        .cart-title { font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem; }
        .product-item { background: #f8fafc; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1rem; position: relative; border: 1px solid #f1f5f9; }
        .product-item .remove-item { position: absolute; top: 0.5rem; right: 0.5rem; color: #ef4444; cursor: pointer; opacity: 0.6; transition: 0.2s; }
        .product-item .remove-item:hover { opacity: 1; }
        .total-section { border-top: 2px dashed #f1f5f9; padding-top: 1.5rem; margin-top: 1.5rem; }
        .total-amount { font-size: 1.75rem; font-weight: 800; color: var(--primary); }
        table.dataTable { border-collapse: separate !important; border-spacing: 0 0.5rem !important; }
        table.dataTable tbody tr { background-color: #fff !important; cursor: pointer; transition: 0.2s; }
        table.dataTable tbody tr:hover { transform: scale(1.01); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        @media (max-width: 1200px) { .split-layout { grid-template-columns: 1fr; } .content-area { margin-left: 0; } }
    </style>
</head>
<body class="bg-main">
    <div class="main-layout">
        <?php include_once '../components/Sidebar.php'; ?>
        
        <div class="content-area">
            <div class="split-layout">
                <!-- Selección de Productos -->
                <div>
                    <div class="card-container mb-4">
                        <div class="mb-4">
                            <span class="badge badge-primary px-3 py-2 mb-2" style="border-radius: 2rem;">Venta en curso</span>
                            <h2 style="font-weight: 700; color: var(--text-main);">Cliente: <?php echo $client['name']; ?></h2>
                            <p style="color: var(--text-muted);">Selecciona los productos de la lista para agregarlos al carrito.</p>
                        </div>

                        <div class="table-responsive">
                            <table id="productsTable" class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Producto</th>
                                        <th>Stock</th>
                                        <th>Precio</th>
                                        <th class="text-right">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr onclick="addProduct(<?php echo $product['id']; ?>, '<?php echo $product['name']; ?>', <?php echo $product['stock']; ?>, <?php echo $product['price']; ?>)">
                                            <td style="color: var(--text-muted); font-weight: 600;"><?php echo $product['id']; ?></td>
                                            <td style="font-weight: 700; color: var(--text-main);"><?php echo $product['name']; ?></td>
                                            <td>
                                                <?php if ($product['stock'] <= 5): ?>
                                                    <span class="badge badge-danger">Bajo: <?php echo $product['stock']; ?></span>
                                                <?php else: ?>
                                                    <span class="badge badge-light"><?php echo $product['stock']; ?> disp.</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="font-weight: 700; color: var(--primary);">$<?php echo number_format($product['price'], 2); ?></td>
                                            <td class="text-right">
                                                <button class="modern-btn modern-btn-primary py-1 px-3" style="width: auto; font-size: 0.75rem;">+</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Carrito / Resumen -->
                <div>
                    <div class="card-container sticky-top" style="top: 2rem; max-height: calc(100vh - 4rem); display: flex; flex-direction: column;">
                        <h4 class="cart-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                            Detalle de Venta
                        </h4>

                        <div id="selectedProductsList" style="flex: 1; overflow-y: auto;">
                            <div class="text-center py-5" id="emptyCart" style="color: var(--text-muted);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-3 opacity-25"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M2 7v13a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V7"/><path d="M2 7h20"/><path d="M10 12h4"/></svg>
                                <p>No hay productos seleccionados</p>
                            </div>
                        </div>

                        <div class="total-section">
                            <div class="d-flex justify-content-between align-items-end mb-4">
                                <div>
                                    <span style="font-size: 0.875rem; color: var(--text-muted); font-weight: 600;">TOTAL</span>
                                </div>
                                <div class="total-amount">$<span id="totalSale">0.00</span></div>
                            </div>
                            
                            <button class="modern-btn modern-btn-primary w-100" id="finalBtn" onclick="finalizeSale()" style="padding: 1rem; display: none;">
                                Finalizar Venta
                            </button>
                            <a href="clientList.php" class="modern-btn w-100 mt-2" style="background: transparent; color: var(--text-muted); border: 1px solid #e1e4e8;">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script>
        let selectedProducts = [];
        let totalSale = 0;

        $(document).ready(function() {
            $('#productsTable').DataTable({
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
                "dom": 'f<"table-scroll"rt>p',
                "pageLength": 10
            });
        });

        function addProduct(id, name, stock, price) {
            const existingProduct = selectedProducts.find(p => p.id === id);
            if (existingProduct) {
                if (existingProduct.quantity < stock) {
                    existingProduct.quantity++;
                    recalculateItem(existingProduct);
                    updateCart();
                } else {
                    alert("Stock máximo alcanzado");
                }
            } else {
                const product = { id, name, stock, price: parseFloat(price), quantity: 1, discount: 0, subtotal: parseFloat(price) };
                selectedProducts.push(product);
                updateCart();
            }
        }

        function recalculateItem(product) {
            const discountedPrice = product.price * (1 - (product.discount / 100));
            product.subtotal = discountedPrice * product.quantity;
        }

        function updateCart() {
            const list = document.getElementById("selectedProductsList");
            const empty = document.getElementById("emptyCart");
            const btn = document.getElementById("finalBtn");
            const totalDisplay = document.getElementById("totalSale");
            
            if (selectedProducts.length === 0) {
                list.innerHTML = "";
                if (empty) list.appendChild(empty);
                else list.innerHTML = `<div class="text-center py-5" id="emptyCart" style="color: var(--text-muted);"><svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-3 opacity-25"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M2 7v13a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V7"/><path d="M2 7h20"/><path d="M10 12h4"/></svg><p>No hay productos seleccionados</p></div>`;
                btn.style.display = "none";
                totalDisplay.textContent = "0.00";
                return;
            }

            if (empty) empty.remove();
            list.innerHTML = "";
            btn.style.display = "block";

            selectedProducts.forEach((product, index) => {
                const item = document.createElement("div");
                item.className = "product-item";
                item.innerHTML = `
                    <div class="remove-item" onclick="removeProduct(${index})">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </div>
                    <div class="mb-2">
                        <span style="font-weight: 700; color: var(--text-main); font-size: 0.9rem;">${product.name}</span>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="d-flex align-items-center gap-2">
                                <div style="display: flex; flex-direction: column;">
                                    <label style="font-size: 0.65rem; color: #64748b; font-weight: 700; margin-bottom: 2px;">CANT.</label>
                                    <input type="number" min="1" max="${product.stock}" value="${product.quantity}" 
                                           oninput="updateQuantity(${index}, this.value)" 
                                           style="width: 55px; height: 32px; border-radius: 0.5rem; border: 1px solid #cbd5e1; padding: 0 0.5rem; font-weight: 600; font-size: 0.85rem;">
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <label style="font-size: 0.65rem; color: #64748b; font-weight: 700; margin-bottom: 2px;">DESC. %</label>
                                    <input type="number" min="0" max="100" value="${product.discount}" 
                                           oninput="updateDiscount(${index}, this.value)" 
                                           style="width: 55px; height: 32px; border-radius: 0.5rem; border: 1px solid #cbd5e1; padding: 0 0.5rem; font-weight: 600; font-size: 0.85rem; color: #166534; background: #f0fdf4;">
                                </div>
                            </div>
                            <div class="text-right">
                                <span style="display: block; font-size: 0.75rem; color: #94a3b8; text-decoration: ${product.discount > 0 ? 'line-through' : 'none'};">$${product.price.toFixed(2)}</span>
                                <span style="font-weight: 800; color: var(--text-main); font-size: 1.05rem;">$<span id="subtotal-${index}">${product.subtotal.toFixed(2)}</span></span>
                            </div>
                        </div>
                    </div>
                `;
                list.appendChild(item);
            });

            calculateTotalSale();
        }

        function updateQuantity(index, quantity) {
            const product = selectedProducts[index];
            let val = parseInt(quantity) || 0;
            if (val > product.stock) val = product.stock;
            if (val < 1) val = 1;
            
            product.quantity = val;
            recalculateItem(product);
            
            const subtotalEl = document.getElementById(`subtotal-${index}`);
            if (subtotalEl) subtotalEl.textContent = product.subtotal.toFixed(2);
            calculateTotalSale();
        }

        function updateDiscount(index, discount) {
            const product = selectedProducts[index];
            let val = parseFloat(discount) || 0;
            if (val > 100) val = 100;
            if (val < 0) val = 0;
            
            product.discount = val;
            recalculateItem(product);
            
            updateCart(); // Update UI to show/hide strikethrough and new price
        }

        function calculateTotalSale() {
            totalSale = selectedProducts.reduce((total, product) => total + product.subtotal, 0);
            document.getElementById("totalSale").textContent = totalSale.toFixed(2);
        }

        function removeProduct(index) {
            selectedProducts.splice(index, 1);
            updateCart();
        }

        function finalizeSale() {
            if (selectedProducts.length === 0) return;

            const btn = document.getElementById("finalBtn");
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = "Procesando...";

            const client_id = <?php echo $client_id; ?>;
            
            // Send the FINAL (discounted) price and the discount percentage to the backend
            const itemsToSave = selectedProducts.map(p => ({
                id: p.id,
                quantity: p.quantity,
                price: (p.price * (1 - (p.discount / 100))).toFixed(2),
                discount: p.discount
            }));

            const saleData = { client_id: client_id, total: totalSale, items: itemsToSave };

            fetch('../../backend/controllers/finalizeSaleHandler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(saleData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = "salesList.php?message=Venta realizada con éxito";
                } else {
                    alert("Error al realizar la venta.");
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error("Error:", error);
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }
    </script>
</body>
</body>
</html>
