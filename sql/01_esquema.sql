-- Esquema completo: calculadora de notas configurable
-- Jerarquía: usuario -> titulacion -> curso -> trimestre -> asignatura -> categoria -> nota

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Soporta doble titulación: un usuario puede tener varias filas aquí (DAW, DAM...)
CREATE TABLE titulaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,      -- ej: "DAW"
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE cursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulacion_id INT NOT NULL,
    nombre VARCHAR(50) NOT NULL,       -- ej: "1º", "2º"
    orden INT DEFAULT 0,               -- para ordenar 1º antes que 2º, no alfabéticamente
    FOREIGN KEY (titulacion_id) REFERENCES titulaciones(id) ON DELETE CASCADE
);

CREATE TABLE trimestres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    curso_id INT NOT NULL,
    nombre VARCHAR(50) NOT NULL,       -- ej: "1er trimestre"
    orden INT DEFAULT 0,
    FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
);

CREATE TABLE asignaturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trimestre_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trimestre_id) REFERENCES trimestres(id) ON DELETE CASCADE
);

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asignatura_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    peso DECIMAL(5,2) NOT NULL,
    tipo ENUM('lista', 'unica') NOT NULL DEFAULT 'lista',
    orden INT DEFAULT 0,
    FOREIGN KEY (asignatura_id) REFERENCES asignaturas(id) ON DELETE CASCADE
);

CREATE TABLE notas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    valor DECIMAL(4,2) NOT NULL,
    fecha DATE DEFAULT NULL,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
);

-- Plantillas de pesos reutilizables entre asignaturas
CREATE TABLE plantillas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE plantilla_categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plantilla_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    peso DECIMAL(5,2) NOT NULL,
    tipo ENUM('lista', 'unica') NOT NULL DEFAULT 'lista',
    FOREIGN KEY (plantilla_id) REFERENCES plantillas(id) ON DELETE CASCADE
);