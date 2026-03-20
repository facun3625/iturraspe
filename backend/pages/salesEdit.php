<?php
// Ruta: control_stock/frontend/pages/salesEdit.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../backend/controllers/authController.php';
$authController = new AuthController();

// Verificar si el usuario está autenticado
if (!$authController->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

require_once '../../backend/controllers/saleController.php';
require_once '../../backend/controllers/clientController.php';
require_once '../../backend/controllers/productController.php';

$sale_id = $_GET['sale_id'];
$saleController = new SaleController();
$saleDetails = $saleController->getSaleDetails($sale_id); // Retorna cabecera y detalle

if (!$saleDetails) {
    die("Venta no encontrada.");
}

$clientController = new ClientController();
$productController = new ProductController();

$client = $clientController->getClientById($saleDetails['client_id'] ?? 0); // Asegúrate de que en tu consulta se retorne client_id o adáptalo
$products = $productController->getProductsWithCalculatedPrice(); // Productos activos

// Para trabajar con la edición, extraemos los items existentes
$saleItems = $saleDetails['items'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Venta #<?php echo $sale_id; ?> | Julio Iturraspe</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
                            <span class="badge badge-warning px-3 py-2 mb-2" style="border-radius: 2rem; color: #92400e; background: #fef3c7;">Editando Venta #<?php echo $sale_id; ?></span>
                            <h2 style="font-weight: 700; color: var(--text-main);">Cliente: <?php echo htmlspecialchars($client['name'] ?? 'Desconocido'); ?></h2>
                            <p style="color: var(--text-muted);">Puedes añadir más productos o modificar los existentes.</p>
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
                                        <tr onclick="addProduct(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>', <?php echo $product['stock']; ?>, <?php echo $product['price']; ?>)">
                                            <td style="color: var(--text-muted); font-weight: 600;"><?php echo $product['id']; ?></td>
                                            <td style="font-weight: 700; color: var(--text-main);"><?php echo htmlspecialchars($product['name']); ?></td>
                                            <td>
                                                <span class="badge badge-light"><?php echo $product['stock']; ?> disp.</span>
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

                <!-- Carrito / Edición -->
                <div>
                    <div class="card-container sticky-top" style="top: 2rem; max-height: calc(100vh - 4rem); display: flex; flex-direction: column;">
                        <h4 class="cart-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Detalle a Guardar
                        </h4>

                        <div id="selectedProductsList" style="flex: 1; overflow-y: auto;">
                            <!-- Dinámico -->
                        </div>

                        <div class="total-section">
                            <div class="d-flex justify-content-between align-items-end mb-4">
                                <div>
                                    <span style="font-size: 0.875rem; color: var(--text-muted); font-weight: 600;">TOTAL ESTIMADO</span>
                                </div>
                                <div class="total-amount">$<span id="totalSale">0.00</span></div>
                            </div>
                            
                            <button class="modern-btn modern-btn-primary w-100" id="saveBtn" onclick="updateSale()" style="padding: 1rem; background: #10b981;">
                                Guardar Cambios
                            </button>
                            <button class="modern-btn w-100 mt-2" onclick="window.history.back()" style="background: transparent; color: var(--text-muted); border: 1px solid #e1e4e8;">
                                Descartar
                            </button>
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
        // Recover original items from PHP
        // price in DB is discounted price. original = price / (1 - (discount/100))
        let selectedProducts = <?php echo json_encode(array_map(function($item) {
            $discount = isset($item['discount']) ? floatval($item['discount']) : 0;
            $savedPrice = floatval($item['price']);
            $originalPrice = ($discount < 100) ? ($savedPrice / (1 - ($discount / 100))) : $savedPrice;
            return [
                'id' => $item['product_id'],
                'name' => $item['product_name'],
                'price' => $originalPrice, // Logic expects base price
                'quantity' => intval($item['quantity']),
                'discount' => $discount,
                'subtotal' => $savedPrice * $item['quantity']
            ];
        }, $saleItems)); ?>;

        let totalSale = 0;

        $(document).ready(function() {
            $('#productsTable').DataTable({
                "language": { "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json" },
                "dom": 'f<"table-scroll"rt>p',
                "pageLength": 10
            });
            updateCart();
        });

        function addProduct(id, name, stock, price) {
            const existingProduct = selectedProducts.find(p => p.id === id);
            if (existingProduct) {
                existingProduct.quantity++;
                recalculateItem(existingProduct);
                updateCart();
            } else {
                const product = { id, name, price: parseFloat(price), quantity: 1, discount: 0, subtotal: parseFloat(price) };
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
            list.innerHTML = "";

            if (selectedProducts.length === 0) {
                list.innerHTML = `<div class="text-center py-5" style="color: var(--text-muted);"><p>No hay productos</p></div>`;
                document.getElementById("totalSale").textContent = "0.00";
                return;
            }

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
                                    <input type="number" min="1" value="${product.quantity}" 
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
                                <span style="font-weight: 800; color: var(--text-main); font-size: 1.05rem;">$${product.subtotal.toFixed(2)}</span>
                            </div>
                        </div>
                    </div>
                `;
                list.appendChild(item);
            });

            calculateTotalSale();
        }

        function updateQuantity(index, quantity) {
            let val = parseInt(quantity) || 1;
            if (val < 1) val = 1;
            selectedProducts[index].quantity = val;
            recalculateItem(selectedProducts[index]);
            updateCart();
        }

        function updateDiscount(index, discount) {
            let val = parseFloat(discount) || 0;
            if (val > 100) val = 100;
            if (val < 0) val = 0;
            selectedProducts[index].discount = val;
            recalculateItem(selectedProducts[index]);
            updateCart();
        }

        function calculateTotalSale() {
            totalSale = selectedProducts.reduce((total, p) => total + p.subtotal, 0);
            document.getElementById("totalSale").textContent = totalSale.toFixed(2);
        }

        function removeProduct(index) {
            selectedProducts.splice(index, 1);
            updateCart();
        }

        function updateSale() {
            if (selectedProducts.length === 0) return;
            const btn = document.getElementById("saveBtn");
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = "Guardando...";

            const itemsToSave = selectedProducts.map(p => ({
                product_id: p.id,
                quantity: p.quantity,
                price: (p.price * (1 - (p.discount / 100))).toFixed(2),
                discount: p.discount
            }));

            const saleData = {
                sale_id: <?php echo $sale_id; ?>,
                client_id: <?php echo $saleDetails['client_id'] ?? 0; ?>,
                total: totalSale,
                items: itemsToSave
            };

            fetch('../../backend/controllers/updateSaleHandler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(saleData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = "clientSales.php?client_id=" + saleData.client_id + "&success=1";
                } else {
                    alert("Error: " + data.message);
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
</html>
