<?php
// Ruta: control_stock/backend/controllers/updateProductHandler.php

require_once 'productController.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $stock = $_POST['stock'];
    $cost = $_POST['cost'];
    $price = !empty($_POST['price']) ? $_POST['price'] : null;

    $productController = new ProductController();
    
    // Verifica si hay una nueva imagen cargada
    $newImagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../frontend/product_img/'; // Cambia la ruta aquí
        $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetFilePath = $uploadDir . $imageName;

        // Mueve el archivo cargado a la carpeta de destino
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $newImagePath = $imageName; // Solo guarda el nombre del archivo

            // Elimina la imagen anterior si existe
            $currentProduct = $productController->getProductById($id);
            if (!empty($currentProduct['image_url']) && file_exists($uploadDir . $currentProduct['image_url'])) {
                unlink($uploadDir . $currentProduct['image_url']);
            }
        }
    }

    // Llama al método de actualización del producto y pasa el nombre de la nueva imagen si existe
    $result = $productController->updateProduct($id, $name, $category_id, $stock, $cost, $price, $newImagePath);

    if ($result) {
        header("Location: ../../frontend/pages/productList.php?edit_success=1");
    } else {
        header("Location: ../../frontend/pages/modifyProduct.php?id=$id&edit_error=1");
    }
    exit();
}
