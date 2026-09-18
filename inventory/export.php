<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_role('Inventory Manager');
require_once __DIR__ . '/../app/models/ProductModel.php';
require_once __DIR__ . '/../app/models/StockModel.php';
require_once __DIR__ . '/../app/controllers/InventoryController.php';

$controller = new InventoryController($pdo);
$export = $_GET['export'] ?? '';

if ($export === 'low_stock') {
    $controller->exportLowStock();
} elseif ($export === 'history') {
    $controller->exportHistory();
} else {
    http_response_code(400);
    exit('Nothing to export.');
}
