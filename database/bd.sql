CREATE DATABASE carta_digital
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_spanish_ci;

USE carta_digital;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo ENUM('restaurante', 'cafeteria') NOT NULL,
    orden INT DEFAULT 0,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio INT DEFAULT 0,
    imagen VARCHAR(255),
    destacado TINYINT(1) DEFAULT 0,
    agotado TINYINT(1) DEFAULT 0,
    activo TINYINT(1) DEFAULT 1,
    CONSTRAINT fk_productos_categorias
        FOREIGN KEY (categoria_id)
        REFERENCES categorias(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE producto_precios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    precio INT NOT NULL,
    orden INT DEFAULT 0,
    activo TINYINT(1) DEFAULT 1,
    CONSTRAINT fk_precios_productos
        FOREIGN KEY (producto_id)
        REFERENCES productos(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE sabores_helado (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    orden INT DEFAULT 0,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE sabores_jugo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    orden INT DEFAULT 0,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO categorias (nombre, tipo, orden, activo) VALUES
('Café', 'cafeteria', 1, 1),
('Té e Infusiones', 'cafeteria', 2, 1),
('Pasteles por Porción', 'cafeteria', 3, 1),
('Helados Artesanales', 'cafeteria', 4, 1),
('Para Comenzar el Día', 'restaurante', 1, 1),
('Sándwiches', 'restaurante', 2, 1),
('Pizzas', 'restaurante', 3, 1),
('Para Compartir', 'restaurante', 4, 1),
('Bebidas y Jugos', 'restaurante', 5, 1);

INSERT INTO productos
(categoria_id, nombre, descripcion, precio, destacado, agotado, activo)
VALUES
(1, 'Americano', 'Café suave preparado con espresso y agua caliente.', 0, 0, 0, 1),
(1, 'Expresso', 'Café corto e intenso.', 0, 0, 0, 1),
(1, 'Cappuccino vainilla', 'Cappuccino con sabor vainilla.', 0, 1, 0, 1),
(1, 'Cappuccino caramelo', 'Cappuccino con sabor caramelo.', 0, 1, 0, 1),
(1, 'Cappuccino avellana', 'Cappuccino con sabor avellana.', 0, 1, 0, 1),
(1, 'Flat white', 'Café espresso con leche vaporizada y textura cremosa.', 0, 0, 0, 1),
(1, 'Mocachino', 'Café espresso con leche y chocolate.', 0, 1, 0, 1),
(1, 'Café Irlandés', 'Café caliente estilo irlandés.', 0, 0, 0, 1),
(1, 'Chocolate caliente', 'Bebida caliente cremosa de chocolate.', 0, 1, 0, 1),

(2, 'Té normal', 'Té caliente tradicional.', 0, 0, 0, 1),

(3, 'Pastel por porción', 'Consulta por sabores disponibles del día.', 0, 0, 0, 1),

(4, 'Helado artesanal', 'Sabores disponibles según stock del día.', 0, 1, 0, 1),

(5, 'Serrano', 'Pan sésamo, jamón serrano, queso crema y aceitunas.', 4500, 0, 0, 1),
(5, 'Champiñón', 'Pan sésamo, champiñones salteados, cebolla caramelizada y queso.', 4500, 0, 0, 1),
(5, 'San Martín', 'Pan sésamo, carne de res, cebolla caramelizada y huevo.', 4500, 0, 0, 1),
(5, 'Arriero', 'Lomo de res, tomate y palta en rodajas, acompañado de huevo frito con tortilla al rescoldo.', 7500, 0, 0, 1),
(5, 'Huevos con tostadas', 'Huevos con tostadas de tortilla al rescoldo.', 3500, 0, 0, 1),
(5, 'Quesadilla jamón queso', 'Quesadilla rellena con jamón y queso.', 2500, 0, 0, 1),
(5, 'Quesadilla mechada queso', 'Quesadilla rellena con carne mechada y queso.', 3000, 0, 0, 1),
(5, 'Empanada napolitana', 'Empanada napolitana.', 2500, 0, 0, 1),
(5, 'Empanada de pino', 'Empanada de pino de pollo o vacuno.', 2500, 0, 0, 1),
(5, 'Empanadas fritas', 'Queso, camarón queso o champiñón queso.', 0, 0, 0, 1),

(6, 'Italiano', 'Pan sésamo, carne mechada, palta y tomate.', 7000, 0, 0, 1),
(6, 'Chacarero', 'Carne mechada, ají verde, porotos verdes y tomate en rodajas.', 7000, 0, 0, 1),
(6, 'Barros Luco', 'Carne mechada con queso fundido.', 7000, 0, 0, 1),

(7, 'Pepperoni', 'Pizza con pepperoni y queso.', 9000, 0, 0, 1),
(7, 'Jamón queso', 'Pizza con jamón y queso.', 9000, 0, 0, 1),
(7, 'Campestre', 'Pollo, cebolla caramelizada y tomate cherry.', 10000, 0, 0, 1),
(7, 'Camarón', 'Camarón, palta y albahaca.', 11000, 0, 0, 1),
(7, 'Vegetariana otoñal', 'Changle, champiñón y aceitunas.', 12000, 0, 0, 1),

(8, 'Chorrillana para 2', 'Papas fritas, cebolla caramelizada, huevo y carne de res, pollo o mixta.', 12000, 0, 0, 1),
(8, 'Pichanga para 2', 'Papas fritas, vienesa, cebolla, palta, tomate y huevo.', 12000, 0, 0, 1),
(8, 'Tabla para 2', 'Carne de res, papas fritas, alitas de pollo apanadas, aritos de cebolla y salsa de la casa.', 14000, 0, 0, 1),
(8, 'Nuggets de pollo con papas fritas', 'Porción individual.', 5500, 0, 0, 1),
(8, 'Salchipapas individual', 'Porción individual.', 5500, 0, 0, 1);
(9, 'Jugos Naturales', 'Limonada', 3000, 0,0,1);

INSERT INTO producto_precios
(producto_id, nombre, precio, orden, activo)
SELECT id, 'Simple', 2500, 1, 1
FROM productos
WHERE nombre = 'Helado artesanal'
LIMIT 1;

INSERT INTO producto_precios
(producto_id, nombre, precio, orden, activo)
SELECT id, 'Doble', 4000, 2, 1
FROM productos
WHERE nombre = 'Helado artesanal'
LIMIT 1;

INSERT INTO sabores_helado (nombre, orden, activo) VALUES
('Chocolate', 1, 1),
('Vainilla', 2, 1),
('Frutilla', 3, 1),
('Manjar', 4, 1),
('Pistacho', 5, 1),
('Cookies & Cream', 6, 1),
('Limón', 7, 1),
('Mango', 8, 1);

INSERT INTO sabores_jugo (nombre, orden, activo) VALUES
('Limonada Menta', 1, 1),
('Limonada Menta Jengibre', 2, 1);

INSERT INTO productos
(categoria_id, nombre, descripcion, precio, destacado, agotado, activo)
VALUES (2,'Basilur Earl Grey',
'Té negro premium aromatizado con aceite natural de bergamota. De sabor elegante, cítrico y delicadamente floral.',
0,1,0,1),

(2,'Basilur English Breakfast',
'Mezcla tradicional de tés negros de cuerpo intenso y sabor robusto, ideal para disfrutar solo o con leche.',
0,1,0,1),

(2,'Basilur Mango & Pineapple',
'Té negro con una exquisita combinación de mango y piña tropical. Una infusión dulce, frutal y muy aromática.',
0,1,0,1),

(2,'Basilur Strawberry & Kiwi',
'Té negro con notas de frutilla y kiwi que ofrecen una infusión fresca, frutal y suavemente dulce.',
0,1,0,1),

(2,'Basilur Masala Chai',
'Mezcla tradicional de té negro con canela, cardamomo, jengibre, clavo y otras especias aromáticas. De sabor intenso y reconfortante.',
0,1,0,1),

(2,'Basilur Ginger',
'Té negro infusionado con jengibre natural. De sabor cálido, ligeramente picante y muy reconfortante.',
0,1,0,1);

/*INSERT INTO usuarios (
    nombre,
    usuario,
    password,
    activo
)
VALUES (
    'Administrador',
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    1
);*/