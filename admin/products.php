<?php require_once '../includes/auth.php'; require_role('Admin'); require '../config/database.php'; require '../app/controllers/AdminController.php'; (new AdminController($pdo))->products();
