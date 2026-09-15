<?php
require_once 'includes/auth.php';
require_login();
$base=project_base_url();
$role=$_SESSION['user']['role']??'';
$routes=['Admin'=>'/admin/dashboard.php','Inventory Manager'=>'/inventory_manager/index.php','Purchase Officer'=>'/purchase_officer/index.php','Sales Staff'=>'/staff/index.php'];
redirect_to($base.($routes[$role]??'/login.php'));
