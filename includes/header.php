<?php
require_once __DIR__ . '/auth.php';
$u = current_user();
$flash = get_flash();
$role = $u['role'] ?? '';
$base = project_base_url();
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$current = basename($scriptName);
$isDashboard = $current === 'index.php' && !str_contains($scriptName, '/admin/') && !str_contains($scriptName, '/profile/');
$isProfile = str_contains($scriptName, '/profile/');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="StockFlow Admin Inventory Management">
<title><?= e($title ?? 'StockFlow Admin') ?></title>
<link rel="stylesheet" href="<?= e($base) ?>/assets/css/style.css">
</head>
<body>
<div class="app-shell">
<header class="topbar">
    <div class="top-left">
        <button class="mobile-toggle" type="button" aria-label="Open menu" onclick="document.body.classList.toggle('sidebar-open')">☰</button>
        <a class="brand" href="<?= e($base) ?>/index.php"><span class="brand-mark">S</span><span>Stock<span class="brand-accent">Flow</span></span></a>
    </div>
    <div class="top-actions">
        <div class="user-chip"><span class="avatar"><?= e(strtoupper(substr($u['name'] ?? 'A', 0, 1))) ?></span><span class="user-meta"><b><?= e($u['name'] ?? 'Admin') ?></b><small><?= e($role) ?></small></span></div>
        <a class="icon-link" href="<?= e($base) ?>/profile/index.php" title="My Profile">⚙</a>
        <a class="logout-link" href="<?= e($base) ?>/logout.php">Logout</a>
    </div>
</header>
<div class="layout">
<aside class="sidebar" id="sidebar">
    <div class="sidebar-label">MAIN MENU</div>
    <a class="nav-link <?= $isDashboard ? 'active' : '' ?>" href="<?= e($base) ?>/index.php"><span>⌂</span> Dashboard</a>
    <?php if ($role === 'Admin'): ?>
        <div class="sidebar-label">ADMINISTRATION</div>
        <a class="nav-link <?= $current === 'users.php' ? 'active' : '' ?>" href="<?= e($base) ?>/admin/users.php"><span>♙</span> User Management</a>
        <a class="nav-link <?= $current === 'products.php' ? 'active' : '' ?>" href="<?= e($base) ?>/admin/products.php"><span>▣</span> Products</a>
        <a class="nav-link <?= $current === 'categories.php' ? 'active' : '' ?>" href="<?= e($base) ?>/admin/categories.php"><span>◇</span> Categories</a>
        <a class="nav-link <?= $current === 'reports.php' ? 'active' : '' ?>" href="<?= e($base) ?>/admin/reports.php"><span>▤</span> Reports</a>
    <?php endif; ?>
    <div class="sidebar-label">ACCOUNT</div>
    <a class="nav-link <?= $isProfile ? 'active' : '' ?>" href="<?= e($base) ?>/profile/index.php"><span>◉</span> My Profile</a>
    <div class="sidebar-bottom"><div class="secure-box"><span>✓</span><div><b>Secure Session</b><small>Protected with session & CSRF.</small></div></div></div>
</aside>
<main class="main-content">
<?php if ($flash): ?><div class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
