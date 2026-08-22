-- Datos de prueba iniciales
-- Ejecutar UNA SOLA VEZ sobre el esquema limpio (01_esquema.sql)

INSERT INTO usuarios (id, email, password_hash) VALUES
    (1, 'yo@daw.local', 'placeholder_sin_login');

INSERT INTO titulaciones (id, usuario_id, nombre) VALUES
    (1, 1, 'DAW'),
    (2, 1, 'DAM');

INSERT INTO cursos (id, titulacion_id, nombre, orden) VALUES
    (1, 1, '1º', 1),
    (2, 1, '2º', 2),
    (3, 2, '1º', 1),
    (4, 2, '2º', 2);

INSERT INTO trimestres (id, curso_id, nombre, orden) VALUES
    (1,  1, '1er trimestre', 1),
    (2,  1, '2º trimestre',  2),
    (3,  1, '3er trimestre', 3),
    (4,  2, '1er trimestre', 1),
    (5,  2, '2º trimestre',  2),
    (6,  2, '3er trimestre', 3),
    (7,  3, '1er trimestre', 1),
    (8,  3, '2º trimestre',  2),
    (9,  3, '3er trimestre', 3),
    (10, 4, '1er trimestre', 1),
    (11, 4, '2º trimestre',  2),
    (12, 4, '3er trimestre', 3);
