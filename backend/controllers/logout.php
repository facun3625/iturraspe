<?php
// Ruta: control_stock/backend/controllers/logout.php
session_start();
session_unset();
session_destroy();

header("Location: ../../frontend/pages/login.php");
exit();
