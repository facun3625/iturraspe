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
  <title>Editar Venta</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <!-- DataTables CSS -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
</head>
<body>
  <?php include_once '../components/navbar.php'; ?>

  <div class="container mt-5">
    <h2 class="text-center">Editar Venta</h2>
    <h4>Cliente: <?php echo $client['name'] ?? 'Desconocido'; ?></h4>

    <!-- Tabla para seleccionar nuevos productos -->
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
            <td><?php echo number_format($product['price'], 2); ?></td>
            <td>
              <button class="btn btn-primary btn-sm" onclick="addProduct(<?php echo $product['id']; ?>, '<?php echo $product['name']; ?>', <?php echo $product['stock']; ?>, <?php echo $product['price']; ?>)">Seleccionar</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- Tabla de productos seleccionados (detalle de venta) -->
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
        <!-- Se cargarán mediante JavaScript -->
      </tbody>
    </table>

    <h5>Total de la Venta: $<span id="totalSale">0.00</span></h5>
    <button class="btn btn-success mt-3" onclick="updateSale()">Actualizar Venta</button>
  </div>

  <script>
    // Inicializamos con los productos ya seleccionados (detalle de la venta existente)
    let selectedProducts = <?php echo json_encode($saleItems); ?>;
    let totalSale = 0;

    function updateSelectedProductsTable() {
      const tbody = document.getElementById("selectedProductsBody");
      tbody.innerHTML = "";
      selectedProducts.forEach((product, index) => {
        const row = document.createElement("tr");
        row.innerHTML = `
          <td>${product.product_id}</td>
          <td>${product.product_name}</td>
          <td>${product.stock ?? 0}</td>
          <td>${parseFloat(product.price).toFixed(2)}</td>
          <td>
            <input type="number" min="1" max="${product.stock ?? 9999}" value="${product.quantity}" onchange="updateQuantity(${index}, this.value)" class="form-control" style="width: 120px;">
          </td>
          <td>$<span id="subtotal-${index}">${(parseFloat(product.price) * product.quantity).toFixed(2)}</span></td>
          <td><button class="btn btn-danger btn-sm" onclick="removeProduct(${index})">Eliminar</button></td>
        `;
        tbody.appendChild(row);
      });
    }

    function addProduct(id, name, stock, price) {
      if (selectedProducts.some(product => product.product_id === id)) {
        alert("El producto ya ha sido agregado.");
        return;
      }
      const product = { product_id: id, product_name: name, stock, price, quantity: 1 };
      selectedProducts.push(product);
      updateSelectedProductsTable();
      calculateTotalSale();
    }

    function updateQuantity(index, quantity) {
      const product = selectedProducts[index];
      product.quantity = quantity;
      document.getElementById(`subtotal-${index}`).textContent = (product.price * quantity).toFixed(2);
      calculateTotalSale();
    }

    function calculateTotalSale() {
      totalSale = selectedProducts.reduce((sum, product) => sum + (product.price * product.quantity), 0);
      document.getElementById("totalSale").textContent = totalSale.toFixed(2);
    }

    function removeProduct(index) {
      selectedProducts.splice(index, 1);
      updateSelectedProductsTable();
      calculateTotalSale();
    }

    function updateSale() {
      if (selectedProducts.length === 0) {
        alert("No hay productos seleccionados para la venta.");
        return;
      }
      const saleData = {
        sale_id: <?php echo $sale_id; ?>,
        client_id: <?php echo $saleDetails['client_id'] ?? 0; ?>,
        total: totalSale,
        items: selectedProducts
      };

      fetch('../../backend/controllers/updateSaleHandler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(saleData)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert("Venta actualizada con éxito.");
          location.reload();
        } else {
          alert("Error al actualizar la venta.");
        }
      })
      .catch(error => console.error("Error en fetch:", error));
    }

    // Inicializamos la tabla y el total
    updateSelectedProductsTable();
    calculateTotalSale();
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
        "language": { "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json" }
      });
    });
  </script>
</body>
</html>
