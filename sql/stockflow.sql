-- StockFlow: Inventory and Stock Management System
-- Run this whole file in phpMyAdmin (SQL tab) on the `stockflow` database.

CREATE DATABASE IF NOT EXISTS stockflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE stockflow;

-- ---------------------------------------------------------------
-- Users (shared by all 4 roles)
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    role       ENUM('Admin','Inventory Manager','Sales Staff','Purchase Officer') NOT NULL,
    status     ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- Categories
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    description VARCHAR(255) NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- Products
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS products (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    sku            VARCHAR(50)  NOT NULL UNIQUE,
    name           VARCHAR(150) NOT NULL,
    category_id    INT NULL,
    unit           VARCHAR(30)  NOT NULL DEFAULT 'pcs',
    quantity       INT NOT NULL DEFAULT 0,
    reorder_level  INT NOT NULL DEFAULT 10,
    cost_price     DECIMAL(10,2) NOT NULL DEFAULT 0,
    selling_price  DECIMAL(10,2) NOT NULL DEFAULT 0,
    status         ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- Stock transactions (Inventory Manager's core table)
-- type = in / out / adjustment
-- for adjustments, quantity is signed (+ or -)
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS stock_transactions (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    product_id    INT NOT NULL,
    type          ENUM('in','out','adjustment') NOT NULL,
    quantity      INT NOT NULL,
    reason        VARCHAR(255) NULL,
    reference_no  VARCHAR(100) NULL,
    created_by    INT NULL,
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- Sample data
-- ---------------------------------------------------------------

-- NOTE: the password column below is a placeholder. See the "Create your login"
-- step in the setup instructions for how to generate a real bcrypt hash and
-- update these rows before you try to log in.
INSERT INTO users (username, password, name, email, role) VALUES
('admin1', 'REPLACE_WITH_HASH', 'System Admin', 'admin@stockflow.local', 'Admin'),
('inv_manager1', 'REPLACE_WITH_HASH', 'Inventory Manager', 'inventory@stockflow.local', 'Inventory Manager');

INSERT INTO categories (name, description) VALUES
('Electronics', 'Electronic devices and accessories'),
('Groceries', 'Everyday grocery items'),
('Stationery', 'Office and school supplies');

INSERT INTO products (sku, name, category_id, unit, quantity, reorder_level, cost_price, selling_price) VALUES
('ELEC-001', 'Wireless Mouse', 1, 'pcs', 45, 15, 5.50, 9.99),
('ELEC-002', 'USB-C Cable 1m', 1, 'pcs', 8, 20, 1.20, 3.50),
('GRO-001',  'Rice 5kg Bag', 2, 'pcs', 0, 10, 6.00, 8.50),
('STA-001',  'A4 Notebook', 3, 'pcs', 120, 30, 0.80, 1.50);
