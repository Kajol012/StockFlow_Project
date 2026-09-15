<?php require_once '../includes/auth.php'; require_role('Sales Staff'); require '../config/database.php'; require '../app/controllers/SalesController.php'; (new SalesController($pdo))->sales();
