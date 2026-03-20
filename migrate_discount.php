<?php
require_once 'backend/config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Check if column exists first (MySQL syntax)
    $stmt = $conn->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_name = 'sale_details' AND column_name = 'discount' AND table_schema = DATABASE()");
    $stmt->execute();
    $exists = $stmt->fetchColumn();

    if (!$exists) {
        $conn->exec("ALTER TABLE sale_details ADD COLUMN discount DECIMAL(5,2) DEFAULT 0.00");
        echo "Column 'discount' added to sale_details successfully.\n";
    } else {
        echo "Column 'discount' already exists.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
