<?php
require_once 'backend/models/saleModel.php';
$saleModel = new SaleModel();
// Let's use a dummy sale_id or just call addSaleDetail on an existing one
// Venta 1169 already exists. Let's update one of its items to have a discount.
require_once 'backend/config/database.php';
$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->prepare("UPDATE sale_details SET discount = 10.00 WHERE sale_id = 1169 AND product_id = 20");
$stmt->execute();
echo "Updated discount for sale 1169, product 20 to 10.00\n";
