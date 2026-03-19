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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Venta</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
</head>
<body>

    <?php include_once '../components/navbar.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center">Realizar Venta</h2>
        
        <h4>Cliente: <?php echo $client['name']; ?></h4>

        <!-- Tabla de productos -->
        <h5 class="mt-4">Seleccionar Productos</h5>
        <table class="table table-bordered" id="productsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Stock</th>
                    <th>Costo</th>
                    <th>Precio</th>
                    <th>Seleccionar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td><?php echo $product['name']; ?></td>
                        <td><?php echo $product['category_name']; ?></td>
                        <td><?php echo $product['stock']; ?></td>
                        <td><?php echo number_format($product['cost'], 2); ?></td>
                        <td><?php echo number_format($product['price'], 2); ?></td> <!-- El precio ya se calcula en el controlador -->
                        <td>
                            <button class="btn btn-primary btn-sm" onclick="addProduct(<?php echo $product['id']; ?>, '<?php echo $product['name']; ?>', <?php echo $product['stock']; ?>, <?php echo $product['price']; ?>)">Seleccionar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Lista de productos seleccionados para la venta -->
        <h5 class="mt-4">Productos Seleccionados</h5>
        <table class="table table-bordered" id="selectedProductsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Stock</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody id="selectedProductsBody">
                <!-- Productos seleccionados se agregarán aquí -->
            </tbody>
        </table>

        <h5>Total de la Venta: $<span id="totalSale">0.00</span></h5>
        <button class="btn btn-success mt-3" onclick="finalizeSale()">Finalizar Venta</button>
    </div>

    <script>
        let selectedProducts = [];
        let totalSale = 0;

        function addProduct(id, name, stock, price) {
            // Verificar si el producto ya fue agregado
            if (selectedProducts.some(product => product.id === id)) {
                alert("El producto ya ha sido agregado.");
                return;
            }

            // Crear el producto seleccionado
            const product = { id, name, stock, price, quantity: 1, subtotal: price };
            selectedProducts.push(product);

            // Actualizar la tabla y el total de la venta
            updateSelectedProductsTable();
            calculateTotalSale();
        }

        function updateSelectedProductsTable() {
            const tbody = document.getElementById("selectedProductsBody");
            tbody.innerHTML = ""; // Limpiar tabla

            selectedProducts.forEach((product, index) => {
                const row = document.createElement("tr");

                row.innerHTML = `
                    <td>${product.id}</td>
                    <td>${product.name}</td>
                    <td>${product.stock}</td>
                    <td>${product.price.toFixed(2)}</td>
                    <td>
                        <input type="number" min="1" max="${product.stock}" value="${product.quantity}" 
                               onchange="updateQuantity(${index}, this.value)" class="form-control" style="width: 120px;">
                    </td>
                    <td>$<span id="subtotal-${index}">${product.subtotal.toFixed(2)}</span></td>
                    <td><button class="btn btn-danger btn-sm" onclick="removeProduct(${index})">Eliminar</button></td>
                `;

                tbody.appendChild(row);
            });
        }

        function updateQuantity(index, quantity) {
            const product = selectedProducts[index];
            product.quantity = quantity;
            product.subtotal = product.price * quantity;

            // Actualizar subtotal y total
            document.getElementById(`subtotal-${index}`).textContent = product.subtotal.toFixed(2);
            calculateTotalSale();
        }

        function calculateTotalSale() {
            totalSale = selectedProducts.reduce((total, product) => total + product.subtotal, 0);
            document.getElementById("totalSale").textContent = totalSale.toFixed(2);
        }

        function removeProduct(index) {
            selectedProducts.splice(index, 1);
            updateSelectedProductsTable();
            calculateTotalSale();
        }

        function finalizeSale() {
            if (selectedProducts.length === 0) {
                alert("No hay productos seleccionados para la venta.");
                return;
            }

            const client_id = <?php echo $client_id; ?>;
            const saleData = {
                client_id: client_id,
                total: totalSale,
                items: selectedProducts
            };

            fetch('../../backend/controllers/finalizeSaleHandler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(saleData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Venta realizada con éxito.");
                    location.reload();
                } else {
                    alert("Error al realizar la venta.");
                }
            })
            .catch(error => console.error("Error en fetch:", error));
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#productsTable').DataTable({
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
</body>
</html>
