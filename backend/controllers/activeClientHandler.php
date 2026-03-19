<?php
// Ruta: control_stock/backend/controllers/deleteClientHandler.php

require_once 'clientController.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $clientController = new ClientController();
    $clientController->activeClient($id);

    header("Location: ../../frontend/pages/clientListHide.php?success=3");
    exit();
}
