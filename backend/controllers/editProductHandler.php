<?php
// Ruta: control_stock/backend/controllers/editProductHandler.php
require_once 'productController.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $cod = $_POST['cod'];
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $stock = $_POST['stock'];
    $list_price = $_POST['list_price'];
    $discount1 = 1 - ($_POST['discount1'] / 100);
    $discount2 = 1 - ($_POST['discount2'] / 100);
    $discount3 = 1 - ($_POST['discount3'] / 100);
    $discount4 = 1 - ($_POST['discount4'] / 100);

    // Calcular el costo basado en el precio de lista y los descuentos
    $cost = ceil($list_price * $discount1 * $discount2 * $discount3 * $discount4);

    // Procesar imagen si fue subida (opcional)
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image_dir = '../../frontend/product_img/';
        if (!is_dir($image_dir)) {
            mkdir($image_dir, 0777, true);
        }

        // Generar un nombre único para la imagen
        $image_name = uniqid('img_', true) . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_full_path = $image_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $image_full_path)) {
            $image_path = 'product_img/' . $image_name;
        } else {
            echo "Error al guardar la imagen.";
            exit();
        }
    }

    $productController = new ProductController();
    // Pasar el parámetro $cod además de los demás datos necesarios
    $result = $productController->updateProduct($id, $cod, $name, $category_id, $stock, $cost, $list_price, $discount1, $discount2, $discount3, $discount4, $image_path);

    if ($result) {
        header("Location: ../../frontend/pages/productList.php?edit_success=1");
    } else {
        header("Location: ../../frontend/pages/productList.php?edit_error=1");
    }
    exit();
}
