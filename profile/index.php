<?php require_once '../includes/auth.php'; require_login(); require '../config/database.php'; require '../app/controllers/ProfileController.php'; (new ProfileController($pdo))->index();
