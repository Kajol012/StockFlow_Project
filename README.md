<<<<<<< HEAD
# StockFlow — Inventory and Stock Management System

A multi-role web application built with **PHP, MySQL, HTML, CSS, JavaScript, AJAX, JSON, Sessions, Cookies and MVC**.

## Four role folders
- `admin/` — Admin dashboard, users, products, categories and reports (your existing Admin module is kept here).
- `inventory_manager/` — stock in/out, stock adjustment, low-stock monitoring and stock history.
- `purchase_officer/` — supplier management, purchase processing and purchase history.
- `staff/` — customer management, sales processing and sales history.

Shared MVC code stays in `app/`, while `config/`, `includes/`, `assets/` and `database/` are shared application infrastructure.

## Role responsibilities
Based on the project proposal:
- **Admin:** overall control; users, products, categories, stock, purchases, sales and reports.
- **Inventory Manager:** view current stock, low-stock monitoring and stock history (view-only).
- **Sales Staff:** customers, sales processing and sales history.
- **Purchase Officer:** suppliers, purchase processing and purchase history.

The system automatically increases stock after a completed purchase and decreases stock after a completed sale. Database transactions are used for these related updates.

## Demo login credentials
- **Admin:** `admin` / `123`
- **Inventory Manager:** `inventory manager` / `222`
- **Purchase Officer:** `purchase officer` / `333`
- **Staff (Sales Staff):** `staff` / `444`

## XAMPP setup
1. Extract this folder to `C:\xampp\htdocs\StockFlow`.
2. Start **Apache** and **MySQL** from XAMPP.
3. Open phpMyAdmin.
4. Import `database/StockSystem.sql`.
5. Open `http://localhost/StockFlow/`.
6. Login with one of the credentials above.

## Database
- Database name: `StockSystem`
- Host: `localhost`
- User: `root`
- Password: empty (default XAMPP)

If your MySQL root password is different, edit only `config/database.php`.

## MVC structure
- `app/models/` — database/data operations
- `app/controllers/` — request handling and business logic
- `app/views/` — HTML presentation
- `admin/`, `inventory_manager/`, `purchase_officer/`, `staff/` — role-specific entry points
- `includes/` — authentication, shared admin layout and role layout
- `ajax/` — AJAX endpoint returning JSON
- `database/StockSystem.sql` — complete database schema + demo data

## Security/technology features
- PHP session authentication and role-based access control
- Remember username cookie
- CSRF protection
- Prepared SQL statements
- Password hashing with `password_hash()` / `password_verify()`
- Server-side and JavaScript validation
- AJAX product search with JSON response
- Transaction-safe stock updates for purchases, sales and stock operations
=======
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
>>>>>>> origin/inventory-manage
