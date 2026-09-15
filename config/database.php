<?php
$host='localhost';
$db='StockSystem';
$user='root';
$pass='';
try{
    $pdo=new PDO("mysql:host={$host};dbname={$db};charset=utf8mb4",$user,$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);
    // Backward-compatible columns for databases created by an earlier StockFlow ZIP.
    $hasCost=(int)$pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='products' AND COLUMN_NAME='cost_price'")->fetchColumn();
    if(!$hasCost){ $pdo->exec("ALTER TABLE products ADD COLUMN cost_price DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER price"); $pdo->exec("UPDATE products SET cost_price=price WHERE cost_price=0"); }
    $hasSaleCost=(int)$pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='sales' AND COLUMN_NAME='cost_price'")->fetchColumn();
    if(!$hasSaleCost){ $pdo->exec("ALTER TABLE sales ADD COLUMN cost_price DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER total"); }
    // Backfill older sales with the most recent recorded purchase cost for that product when available.
    if(!$hasSaleCost){ $pdo->exec("UPDATE sales s SET s.cost_price=COALESCE((SELECT pr.unit_price FROM purchases pr WHERE pr.product_id=s.product_id AND pr.status='Completed' ORDER BY pr.created_at DESC, pr.id DESC LIMIT 1),(SELECT p.cost_price FROM products p WHERE p.id=s.product_id),0) WHERE s.cost_price=0"); }
    $hasPurchaseSell=(int)$pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='purchases' AND COLUMN_NAME='selling_price'")->fetchColumn();
    if(!$hasPurchaseSell){ $pdo->exec("ALTER TABLE purchases ADD COLUMN selling_price DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER total"); $pdo->exec("UPDATE purchases pr JOIN products p ON p.id=pr.product_id SET pr.selling_price=p.price WHERE pr.selling_price=0"); }
}catch(PDOException $e){http_response_code(500);exit('Database connection failed. Please import database/StockSystem.sql and check config/database.php.');}
