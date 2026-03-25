<?php
// Ruta: control_stock/frontend/pages/priceList.php

require_once 'backend/controllers/productController.php';
$productController = new ProductController();

try {
    $products = $productController->getProductsWithCalculatedPrice(); // Usamos la función para obtener productos con el precio calculado

    // Ordenar los productos alfabéticamente por 'name'
    usort($products, function($a, $b) {
        return strcmp($a['name'], $b['name']);
    });
} catch (Exception $e) {
    $products = [];
    $error = "Error al cargar los productos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Precios | Julio Iturraspe</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="frontend/assets/css/modern-system.css">
    <style>
        .page-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        .list-header {
            margin-bottom: 2.5rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 1.5rem;
        }
        .header-content h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 0.5rem;
            letter-spacing: -0.025em;
        }
        .header-content p {
            color: var(--text-muted);
            font-size: 1.1rem;
        }
        .search-container {
            position: relative;
            margin-bottom: 2rem;
        }
        .search-icon {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
        }
        .search-input {
            padding-left: 3.25rem !important;
            height: 3.5rem;
            font-size: 1rem;
            box-shadow: var(--shadow-soft);
        }
        .product-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 0.75rem;
        }
        .product-table thead th {
            border: none;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 1rem 1.5rem;
        }
        .product-row {
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .product-row:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-soft);
        }
        .product-row td {
            padding: 1.25rem 1.5rem;
            vertical-align: middle;
            border: none;
        }
        .product-row td:first-child { border-radius: 1rem 0 0 1rem; }
        .product-row td:last-child { border-radius: 0 1rem 1rem 0; }
        
        .product-id { font-family: monospace; color: var(--text-muted); font-size: 0.85rem; }
        .product-name { font-weight: 700; color: var(--text-main); font-size: 1.05rem; }
        .category-badge {
            background: #f1f5f9;
            color: #475569;
            padding: 0.35rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .price-tag {
            font-weight: 800;
            color: var(--primary);
            font-size: 1.15rem;
        }
        .btn-view-img {
            background: #eff6ff;
            color: var(--primary);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        .btn-view-img:hover {
            background: var(--primary);
            color: white;
        }
        .export-actions {
            display: flex;
            gap: 0.75rem;
        }
        .btn-export {
            width: auto;
            white-space: nowrap;
        }
        
        .copy-link-banner {
            background: white;
            padding: 1rem 1.5rem;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            border: 1px dashed #cbd5e1;
        }
        
        @media (max-width: 768px) {
            .list-header { flex-direction: column; align-items: flex-start; }
            .product-name { font-size: 0.95rem; }
            .product-row td { padding: 1rem 0.75rem; }
            .hide-mobile { display: none; }
        }
    </style>
</head>
<body class="bg-main">

    <div class="page-container">
        <header class="list-header">
            <div class="header-content">
                <h1>Lista de Precios</h1>
                <p>Catálogo actualizado de productos</p>
            </div>
            <div class="export-actions">
                <button id="exportCsv" class="modern-btn btn-export" style="background: #10b981; color: white;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    CSV
                </button>
                <button id="exportExcel" class="modern-btn btn-export" style="background: var(--primary); color: white;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M8 13h2"/><path d="M8 17h2"/><path d="M14 13h2"/><path d="M14 17h2"/></svg>
                    Excel
                </button>
            </div>
        </header>

        <div class="search-container">
            <div class="search-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
            <input type="text" id="searchInput" class="modern-input search-input" placeholder="Buscar por nombre, código o categoría...">
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger" style="border-radius: 1rem; border: none; box-shadow: var(--shadow-soft);"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="table-responsive" style="overflow-x: auto;">
            <table class="product-table" id="priceTable">
                <thead>
                    <tr>
                        <th class="hide-mobile">Cód.</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th class="text-right">Imagen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr class="product-row">
                            <td class="hide-mobile"><span class="product-id"><?php echo htmlspecialchars($product['id']); ?></span></td>
                            <td><div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div></td>
                            <td><span class="category-badge"><?php echo htmlspecialchars($product['category_name']); ?></span></td>
                            <td><div class="price-tag">$<?php echo number_format($product['price'], 2, ',', '.'); ?></div></td>
                            <td class="text-right">
                                <?php if (!empty($product['image_url'])): ?>
                                    <button class="btn-view-img" onclick="showImageModal('<?php echo $product['image_url']; ?>')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                    </button>
                                <?php else: ?>
                                    <span style="color: #cbd5e1; font-size: 0.75rem;">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para la imagen -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 1.5rem; border: none; overflow: hidden; box-shadow: var(--shadow-strong);">
                <div class="modal-body p-0 position-relative">
                    <button type="button" class="close position-absolute" data-dismiss="modal" style="right: 1.5rem; top: 1.5rem; z-index: 10; background: white; width: 32px; height: 32px; border-radius: 50%; opacity: 0.8; display: flex; align-items: center; justify-content: center;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <img id="modalImage" src="" class="img-fluid w-100" alt="Imagen del producto" style="min-height: 200px; background: #f8fafc;">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
    function showImageModal(imageUrl) {
        var fullPath = 'frontend/' + imageUrl;
        $('#modalImage').attr('src', fullPath);
        $('#imageModal').modal('show');
    }

    $(document).ready(function(){
        $("#searchInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#priceTable tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });

    function exportTableToCSV(filename) {
        var csv = [];
        var rows = document.querySelectorAll("#priceTable tr");
        for (var i = 0; i < rows.length; i++) {
            var row = [], cols = rows[i].querySelectorAll("td, th");
            for (var j = 0; j < cols.length; j++) {
                var data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").replace(/"/g, '""');
                row.push('"' + data + '"');
            }
            csv.push(row.join(","));
        }
        var csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
        var downloadLink = document.createElement("a");
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
    }

    function exportTableToExcelJS(filename = 'lista_de_precios.xlsx'){
        var table = document.getElementById("priceTable");
        var workbook = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });
        XLSX.writeFile(workbook, filename);
    }

    document.getElementById("exportCsv").addEventListener("click", function () {
        exportTableToCSV('lista_de_precios.csv');
    });
    document.getElementById("exportExcel").addEventListener("click", function () {
        exportTableToExcelJS();
    });
    </script>
</body>
</html>
