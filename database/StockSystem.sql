-- StockFlow: Inventory and Stock Management System
-- Database: StockSystem
-- XAMPP default: MySQL root user with empty password

CREATE DATABASE IF NOT EXISTS StockSystem CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE StockSystem;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS purchase_returns;
DROP TABLE IF EXISTS sales_returns;
DROP TABLE IF EXISTS stock_transactions;
DROP TABLE IF EXISTS sales;
DROP TABLE IF EXISTS purchases;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS suppliers;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(30) NOT NULL UNIQUE,
 name VARCHAR(100) NOT NULL,
 email VARCHAR(150) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL,
 role ENUM('Admin','Inventory Manager','Sales Staff','Purchase Officer') NOT NULL,
 status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE categories (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE products (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(150) NOT NULL,
 sku VARCHAR(80) NOT NULL UNIQUE,
 category_id INT UNSIGNED NULL,
 price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
 cost_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
 quantity INT NOT NULL DEFAULT 0,
 reorder_level INT NOT NULL DEFAULT 5,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_products_category FOREIGN KEY(category_id) REFERENCES categories(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE customers (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 phone VARCHAR(40) NULL,
 email VARCHAR(150) NULL,
 address VARCHAR(255) NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE suppliers (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 phone VARCHAR(40) NULL,
 email VARCHAR(150) NULL,
 address VARCHAR(255) NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sales (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 customer_id INT UNSIGNED NULL,
 product_id INT UNSIGNED NOT NULL,
 quantity INT NOT NULL,
 unit_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
 total DECIMAL(12,2) NOT NULL,
 cost_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
 status ENUM('Completed','Cancelled') NOT NULL DEFAULT 'Completed',
 user_id INT UNSIGNED NOT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_sales_customer FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE SET NULL,
 CONSTRAINT fk_sales_product FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE RESTRICT,
 CONSTRAINT fk_sales_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE purchases (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 supplier_id INT UNSIGNED NULL,
 product_id INT UNSIGNED NOT NULL,
 quantity INT NOT NULL,
 unit_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
 total DECIMAL(12,2) NOT NULL,
 selling_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
 status ENUM('Completed','Cancelled') NOT NULL DEFAULT 'Completed',
 user_id INT UNSIGNED NOT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_purchases_supplier FOREIGN KEY(supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL,
 CONSTRAINT fk_purchases_product FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE RESTRICT,
 CONSTRAINT fk_purchases_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sales_returns (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 sale_id INT UNSIGNED NOT NULL,
 product_id INT UNSIGNED NOT NULL,
 customer_id INT UNSIGNED NULL,
 quantity INT NOT NULL,
 refund_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
 reason VARCHAR(255) NULL,
 user_id INT UNSIGNED NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_sales_returns_sale FOREIGN KEY(sale_id) REFERENCES sales(id) ON DELETE RESTRICT,
 CONSTRAINT fk_sales_returns_product FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE RESTRICT,
 CONSTRAINT fk_sales_returns_customer FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE SET NULL,
 CONSTRAINT fk_sales_returns_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE purchase_returns (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 purchase_id INT UNSIGNED NOT NULL,
 product_id INT UNSIGNED NOT NULL,
 supplier_id INT UNSIGNED NULL,
 quantity INT NOT NULL,
 refund_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
 reason VARCHAR(255) NULL,
 user_id INT UNSIGNED NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_purchase_returns_purchase FOREIGN KEY(purchase_id) REFERENCES purchases(id) ON DELETE RESTRICT,
 CONSTRAINT fk_purchase_returns_product FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE RESTRICT,
 CONSTRAINT fk_purchase_returns_supplier FOREIGN KEY(supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL,
 CONSTRAINT fk_purchase_returns_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE stock_transactions (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 product_id INT UNSIGNED NOT NULL,
 type ENUM('IN','OUT','ADJUSTMENT') NOT NULL,
 quantity INT NOT NULL,
 note VARCHAR(255) NULL,
 user_id INT UNSIGNED NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_stock_product FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE,
 CONSTRAINT fk_stock_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Demo accounts requested for the project.
-- Admin: admin / 123 (existing Admin account kept)
-- Inventory Manager: inventory manager / 222
-- Purchase Officer: purchase officer / 333
-- Staff (Sales Staff): staff / 444
INSERT INTO users(username,name,email,password,role,status) VALUES
('admin','System Admin','admin@stockflow.local','$2y$12$P1T7IfMeVIV7/WhkrrIVie1WOump4vZN8j7ESeaLd7y3Vcg1xoKbe','Admin','Active'),
('inventory manager','Inventory Manager','inventory@stockflow.local','$2y$12$I/DTY0VNOXe6/31OC.1iPOOW3NsqQzpBb7F1hmgGKErONUkxHxf1C','Inventory Manager','Active'),
('purchase officer','Purchase Officer','purchase@stockflow.local','$2y$12$8k5PTS1ciHak1efqtFZxrOYCrlYKTHRKxL7EoMAm/nduhO1fsE1Me','Purchase Officer','Active'),
('staff','Sales Staff','staff@stockflow.local','$2y$12$mDnH6kZaFi3L42GRe/o0p.cXZsVHCPPv4TJsHhN5EjlVI8vYTKLly','Sales Staff','Active');

INSERT INTO categories(name) VALUES ('Electronics'),('Office Supplies'),('Accessories');
INSERT INTO products(name,sku,category_id,price,cost_price,quantity,reorder_level) VALUES
('Wireless Mouse','WM-100',1,850.00,850.00,25,5),
('Keyboard','KB-200',1,1200.00,1200.00,4,5),
('Notebook','NB-300',2,180.00,180.00,50,10),
('USB Cable','UC-400',3,350.00,350.00,12,5);
INSERT INTO customers(name,phone,email,address) VALUES ('Walk-in Customer','01700000000','customer@example.com','Dhaka');
INSERT INTO suppliers(name,phone,email,address) VALUES ('ABC Suppliers','01800000000','supplier@example.com','Dhaka');
