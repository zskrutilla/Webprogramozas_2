-- Task01 schema
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  price_ft INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO products (name, price_ft) VALUES
  ('Kenyér', 899),
  ('Tej', 499),
  ('Sajt', 1299)
ON DUPLICATE KEY UPDATE name=VALUES(name);
