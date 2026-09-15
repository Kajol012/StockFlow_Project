<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_role('Inventory Manager');
require_once __DIR__ . '/../app/models/ProductModel.php';
require_once __DIR__ . '/../app/models/StockModel.php';
require_once __DIR__ . '/../app/controllers/InventoryController.php';

(new InventoryController($pdo))->stockHistory();
