<?php
require_once 'includes/auth.php';
require 'config/database.php';
require 'app/models/UserModel.php';

if (current_user()) {
    redirect_to(project_base_url() . '/index.php');
}

$m = new UserModel($pdo);
$err = '';
$username = '';
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $username = trim($_POST['username'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($username === '' || !preg_match('/^[A-Za-z0-9_.-]{3,30}$/', $username)) {
        $err = 'Username must be 3–30 characters and can contain only letters, numbers, dot, underscore and hyphen.';
    } elseif ($name === '' || mb_strlen($name) < 2 || mb_strlen($name) > 100) {
        $err = 'Please enter a valid full name.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $err = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $err = 'Password and confirm password do not match.';
    } elseif ($m->findByUsernameAnyStatus($username)) {
        $err = 'This username is already registered. Please choose another username.';
    } elseif ($m->findByEmailAnyStatus($email)) {
        $err = 'This email is already registered. Please use another email.';
    } else {
        try {
            $m->create([
                'username' => $username,
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'role' => 'Sales Staff',
                'status' => 'Active'
            ]);
            flash('success', 'Account created successfully. Please sign in.');
            redirect_to(project_base_url() . '/login.php');
        } catch (Throwable $e) {
            $err = 'Registration could not be completed. Please check the information and try again.';
        }
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Sign Up · StockFlow</title><link rel="stylesheet" href="assets/css/style.css"></head><body><div class="auth"><div class="auth-box"><div class="auth-logo"><span class="brand-mark">S</span><div><h1>Stock<span class="brand-accent">Flow</span></h1><div class="muted auth-sub">Create your account</div></div></div><div class="notice neutral"><b>New to StockFlow?</b><br><span class="muted">Create an account to access the Sales Staff dashboard.</span></div><?php if($err):?><div class="flash error"><?=e($err)?></div><?php endif;?><form method="post" autocomplete="on" data-validate-form><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><div class="field"><label for="username">Username</label><input id="username" name="username" value="<?=e($username)?>" required minlength="3" maxlength="30" pattern="[A-Za-z0-9_.-]{3,30}" autocomplete="username" placeholder="e.g. afreen123"><span class="helper">3–30 characters: letters, numbers, dot, underscore or hyphen.</span></div><div class="field"><label for="name">Full Name</label><input id="name" name="name" value="<?=e($name)?>" required minlength="2" maxlength="100" autocomplete="name" placeholder="Enter your full name"></div><div class="field"><label for="email">Email</label><input id="email" type="email" name="email" value="<?=e($email)?>" required maxlength="150" autocomplete="email" placeholder="you@example.com"></div><div class="field"><label for="password">Password</label><input id="password" type="password" name="password" minlength="6" required autocomplete="new-password" placeholder="At least 6 characters"></div><div class="field"><label for="confirm_password">Confirm Password</label><input id="confirm_password" type="password" name="confirm_password" minlength="6" required autocomplete="new-password" placeholder="Enter the password again"></div><button class="btn" type="submit">Create Account</button></form><p class="helper" style="margin-top:16px">Already have an account? <a class="link" href="login.php">Sign In</a></p></div></div><script src="assets/js/app.js"></script></body></html>
