CREATE TABLE IF NOT EXISTS contactos (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL,
    telefono VARCHAR(50),
    producto VARCHAR(100),
    calificacion INT NOT NULL,
    mensaje TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
