-- 1. Procedimiento para Insertar un Nuevo Trabajo en HistorialHerrero
-- Inserta un registro en el historial de trabajos del herrero, sin incluir estado de herramienta
DELIMITER //

CREATE PROCEDURE InsertarHistorialHerrero (
    IN p_idEquino INT,
    IN p_idUsuario INT,
    IN p_fecha DATE,
    IN p_trabajoRealizado TEXT,
    IN p_herramientasUsadas TEXT,
    IN p_observaciones TEXT
)
BEGIN
    INSERT INTO HistorialHerrero (
        idEquino, idUsuario, fecha, trabajoRealizado, herramientasUsadas, observaciones
    ) VALUES (
        p_idEquino, p_idUsuario, p_fecha, p_trabajoRealizado, p_herramientasUsadas, p_observaciones
    );
END //

DELIMITER ;


-- 2. Procedimiento para Insertar una Herramienta Usada en HerramientasUsadasHistorial
-- Registra una herramienta utilizada en un trabajo específico sin el estado de herramienta
DELIMITER //

CREATE PROCEDURE InsertarHerramientaUsada (
    IN p_idHistorialHerrero INT,
    IN p_idHerramienta INT
)
BEGIN
    INSERT INTO HerramientasUsadasHistorial (
        idHistorialHerrero, idHerramienta
    ) VALUES (
        p_idHistorialHerrero, p_idHerramienta
    );
END //

DELIMITER ;


-- 3. Procedimiento para Consultar el Historial Completo de un Equino
-- Retorna el historial de trabajos de herrería para un equino específico, con detalles generales del trabajo
DELIMITER //

CREATE PROCEDURE ConsultarHistorialEquino (
    IN p_idEquino INT
)
BEGIN
    SELECT H.idHistorialHerrero, H.fecha, H.trabajoRealizado, H.herramientasUsadas, H.observaciones
    FROM HistorialHerrero H
    WHERE H.idEquino = p_idEquino
    ORDER BY H.fecha DESC;
END //

DELIMITER ;


-- 4. Procedimiento para Consultar el Estado Actual de Herramientas
-- Muestra el estado actual de cada herramienta utilizada en trabajos recientes, sin incluir estado inicial y final
DELIMITER //

CREATE PROCEDURE ConsultarEstadoActualHerramientas ()
BEGIN
    SELECT H.idHerramienta, E.descripcionEstado AS estadoActual
    FROM HerramientasUsadasHistorial H
    JOIN EstadoHerramienta E ON H.idHerramienta = E.idEstado
    ORDER BY H.idHistorialHerrero DESC;
END //

DELIMITER ;


-- 5. Procedimiento para Insertar un Nuevo Estado de Herramienta en EstadoHerramienta
-- Inserta un nuevo estado de herramienta en la tabla EstadoHerramienta
DELIMITER //

CREATE PROCEDURE InsertarEstadoHerramienta (
    IN p_descripcionEstado VARCHAR(50)
)
BEGIN
    INSERT INTO EstadoHerramienta (descripcionEstado)
    VALUES (p_descripcionEstado);
END //

DELIMITER ;


CALL InsertarHistorialHerrero(1, 1, '2023-01-01', 'Recorte de casco', 'Herradura de acero', 'Observación de prueba');
