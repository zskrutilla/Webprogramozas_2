-- Task04 schema
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  price_ft INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO products (name, price_ft) VALUES
 ('Kenyér', 899),('Tej', 499),('Sajt',1299),('Vaj',1199),('Tészta',699),
 ('Paradicsom',799),('Paprika',899),('Alma',599),('Banán',999),('Kávé',1890),
 ('Tea',1290),('Csoki',1090),('Keksz',790),('Müzli',1490),('Jogurt',399),
 ('Szalámi',2190),('Sonka',2390),('Tojás (10db)',999),('Liszt',499),('Cukor',599)
ON DUPLICATE KEY UPDATE name=VALUES(name);
