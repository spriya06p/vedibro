-- FRESH INSTALL ONLY: drops the old tables of this project.
CREATE DATABASE IF NOT EXISTS crackers CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE crackers;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS order_items, orders, products, categories, states, settings, admin_users;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0,   -- 80 = "80% Discount", 0 = "Net Rate"
  sort_order INT NOT NULL DEFAULT 0
);
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  code VARCHAR(40) NOT NULL UNIQUE,
  name VARCHAR(180) NOT NULL,
  content VARCHAR(60) NOT NULL DEFAULT '1 Box',
  actual_price DECIMAL(10,2) NOT NULL,                -- MRP. Selling price = MRP - category discount
  image VARCHAR(255) DEFAULT NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);
CREATE TABLE states (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL UNIQUE,
  min_order DECIMAL(10,2) NOT NULL DEFAULT 0,
  active TINYINT(1) NOT NULL DEFAULT 1
);
CREATE TABLE settings (k VARCHAR(60) PRIMARY KEY, v VARCHAR(255) NOT NULL);
CREATE TABLE admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(80) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL
);
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_no VARCHAR(40) NOT NULL UNIQUE,
  state VARCHAR(80) NOT NULL,
  city VARCHAR(100) DEFAULT '',
  customer_name VARCHAR(150) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  email VARCHAR(150) DEFAULT '',
  address TEXT NOT NULL,
  net_total DECIMAL(10,2) NOT NULL,     -- total at MRP
  savings DECIMAL(10,2) NOT NULL,       -- discount total
  subtotal DECIMAL(10,2) NOT NULL,      -- net - discount
  packing_percent DECIMAL(5,2) NOT NULL DEFAULT 0,
  packing DECIMAL(10,2) NOT NULL DEFAULT 0,
  round_off DECIMAL(6,2) NOT NULL DEFAULT 0,
  overall_total DECIMAL(10,2) NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'New',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (status), INDEX (created_at)
);
CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  product_code VARCHAR(40) NOT NULL,
  product_name VARCHAR(180) NOT NULL,
  content VARCHAR(60) DEFAULT '',
  actual_price DECIMAL(10,2) NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  quantity INT NOT NULL,
  line_total DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

INSERT INTO settings VALUES ('packing_percent','0');
INSERT INTO states (name,min_order) VALUES
('Tamil Nadu',3000),('Kerala',5000),('Karnataka',5000),('Telangana',5000),('Andhra Pradesh',5000);
INSERT INTO categories (name,discount_percent,sort_order) VALUES
('Sound Crackers',80,1),('Ground Chakkar',80,2),('Bomb',80,3),('Flower Pots',80,4),
('Sparklers',80,5),('Gift Boxes',0,6),('Digital Crackers',0,7);
-- Sample products only. Add your own in Admin > Products or Admin > Import CSV.
INSERT INTO products (category_id,code,name,content,actual_price) VALUES
(1,'1','Sample Sound Cracker','1 Pkt',100),
(2,'41','Sample Ground Chakkar','1 Box',190),
(4,'101','Sample Flower Pot','1 Box',250),
(5,'201','10 Cm Electric Sparklers','1 Box',100),
(6,'251','20 Items Gift Box','1 Box',300);
-- Default admin: admin / password  (CHANGE IT: Admin > Password)
INSERT INTO admin_users (username,password_hash) VALUES
('admin','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCq9xGZ8nQ0jQvV4d6aW');
