<?php

// Activar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'productController.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica si todos los campos necesarios están presentes
    if (isset($_POST['cod'], $_POST['category_id'], $_POST['name'], $_POST['description'], $_POST['stock'], $_POST['low_stock_level'], $_POST['list_price'], $_POST['discount1'], $_POST['discount2'], $_POST['discount3'], $_POST['discount4'])) {
        
        // Captura los campos del formulario
        $cod = $_POST['cod'];
        $category_id = $_POST['category_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $stock = $_POST['stock'];
        $low_stock_level = $_POST['low_stock_level'];
        $list_price = $_POST['list_price'];
        $discount1 = 1 - ($_POST['discount1'] / 100);
        $discount2 = 1 - ($_POST['discount2'] / 100);
        $discount3 = 1 - ($_POST['discount3'] / 100);
        $discount4 = 1 - ($_POST['discount4'] / 100);

        // Calcular costo
        $cost = ceil($list_price * $discount1 * $discount2 * $discount3 * $discount4);

        // Procesar imagen si fue subida
        $image_url = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $image_dir = '../../frontend/product_img/'; // Ruta de la carpeta de imágenes
            if (!is_dir($image_dir)) {
                mkdir($image_dir, 0777, true); // Crear la carpeta si no existe
            }
            
            // Crear un nombre único para la imagen y guardarla en la ruta especificada
            $image_name = uniqid('img_', true) . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_path = $image_dir . $image_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                $image_url = 'product_img/' . $image_name; // Guardar solo la ruta relativa en $image_url
            } else {
                echo "Error al guardar la imagen.";
                exit();
            }
        }

        // Inserta el producto en la base de datos
        $productController = new ProductController();
        $result = $productController->addProduct($cod, $category_id, $name, $description, $stock, $low_stock_level, $cost, $list_price, $discount1, $discount2, $discount3, $discount4, $image_url);

        if ($result) {
            header("Location: ../../frontend/pages/addProduct.php?success=1");
        } else {
            header("Location: ../../frontend/pages/addProduct.php?error=1");
        }
        exit();
    } else {
        echo "Faltan datos del formulario.";
    }
}
