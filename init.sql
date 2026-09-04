
DROP TABLE IF EXISTS contactos;

CREATE TABLE contactos (
    id              SERIAL PRIMARY KEY,
    nombre          VARCHAR(50)  NOT NULL CHECK (char_length(nombre) >= 3),
    correo          VARCHAR(150) NOT NULL,
    telefono        VARCHAR(10)  NOT NULL,
    ley_favorita    VARCHAR(100) NOT NULL,
    calificacion    SMALLINT     NOT NULL CHECK (calificacion BETWEEN 1 AND 5),
    mensaje         VARCHAR(300) NOT NULL CHECK (char_length(mensaje) >= 10),
    fecha_registro  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_contactos_fecha ON contactos (fecha_registro DESC);
