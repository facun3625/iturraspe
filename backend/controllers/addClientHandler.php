<?php
// Ruta: control_stock/backend/controllers/addClientHandler.php

require_once 'clientController.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $cuit = $_POST['cuit'];
    $address = $_POST['address'];

    $clientController = new ClientController();
    $result = $clientController->addClient($name, $phone, $email, $cuit, $address);

    if ($result) {
        header("Location: ../../frontend/pages/clientList.php?success=1");
    } else {
        header("Location: ../../frontend/pages/addClient.php?error=1");
    }
    exit();
}
