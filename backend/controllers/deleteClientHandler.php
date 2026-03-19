<?php
// Ruta: control_stock/backend/controllers/deleteClientHandler.php

require_once 'clientController.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $clientController = new ClientController();
    $clientController->deleteClient($id);

    header("Location: ../../frontend/pages/clientList.php?success=3");
    exit();
}
