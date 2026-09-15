# StockFlow — Inventory Manager Role Package

This ZIP is the **Inventory Manager-only** version of the StockFlow Inventory & Stock Management System.

## Included
- Sign In / Login
- Sign Up / Registration
- Logout
- My Profile (each user can edit only their own profile)
- Session-based authentication and role protection
- CSRF protection
- Cookie-based remembered username
- MVC structure (Controllers / Models / Views)
- AJAX + JSON product search support
- MySQL database connection and SQL file
- Only the **Inventory Manager** operational module is included; other role modules are intentionally excluded so the demo clearly shows this role's work.

## Demo login
Username: `inventory`
Password: `123`

If the SQL file contains different seeded credentials in your copy, use those credentials.

## Run
1. Put the `StockFlow` folder inside `htdocs`.
2. Start Apache and MySQL from XAMPP.
3. Create/import database `StockSystem` using `database/StockSystem.sql`.
4. Check `config/database.php` if your MySQL username/password is different.
5. Open `http://localhost/StockFlow/`
6. Sign in. The system redirects to the **Inventory Manager** dashboard.

## MVC
- Controller: `app/controllers/`
- Model: `app/models/`
- View: `app/views/`
- Role entry pages: `inventory_manager/`
- Shared authentication: `includes/auth.php`
