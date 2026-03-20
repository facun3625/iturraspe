<?php
// migration_operating_costs.php
require_once 'backend/config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    $query = "CREATE TABLE IF NOT EXISTS operating_costs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category VARCHAR(50) NOT NULL,
        description VARCHAR(255) NOT NULL,
        amount DECIMAL(15, 2) NOT NULL,
        date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $db->exec($query);
    echo "Table 'operating_costs' created successfully.\n";

} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage() . "\n";
}
