<?php
// Ruta: control_stock/backend/controllers/updateClientHandler.php

require_once 'clientController.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $cuit = $_POST['cuit'];
    $address = $_POST['address'];

    $clientController = new ClientController();
    $result = $clientController->updateClient($id, $name, $phone, $email, $cuit, $address);

    if ($result) {
        header("Location: ../../frontend/pages/clientList.php?success=2");
    } else {
        header("Location: ../../frontend/pages/clientList.php?error=1");
    }
    exit();
}
