--  1.Procedimiento para Insertar un Nuevo Trabajo en HistorialHerrero  

DELIMITER //

CREATE PROCEDURE InsertarHistorialHerrero (
    IN p_idEquino INT,
    IN p_idUsuario INT,
    IN p_fecha DATE,
    IN p_trabajoRealizado TEXT,
    IN p_herramientasUsadas TEXT,
    IN p_estadoInicio INT,
    IN p_estadoFin INT,
    IN p_observaciones TEXT
)
BEGIN
    INSERT INTO HistorialHerrero (
        idEquino, idUsuario, fecha, trabajoRealizado, herramientasUsadas, estadoInicio, estadoFin, observaciones
    ) VALUES (
        p_idEquino, p_idUsuario, p_fecha, p_trabajoRealizado, p_herramientasUsadas, p_estadoInicio, p_estadoFin, p_observaciones
    );
END //

DELIMITER ;

-- 2.Procedimiento para Insertar una Herramienta Usada en HerramientasUsadasHistorial ------------------------------------------------------------------------------------------------------
DELIMITER //

CREATE PROCEDURE InsertarHerramientaUsada (
    IN p_idHistorialHerrero INT,
    IN p_idHerramienta INT,
    IN p_estadoInicio INT,
    IN p_estadoFin INT
)
BEGIN
    INSERT INTO HerramientasUsadasHistorial (
        idHistorialHerrero, idHerramienta, estadoInicio, estadoFin
    ) VALUES (
        p_idHistorialHerrero, p_idHerramienta, p_estadoInicio, p_estadoFin
    );
END //

DELIMITER ;


-- 3. Procedimiento para Consultar el Historial Completo de un Equino -------------------------
DELIMITER //

CREATE PROCEDURE ConsultarHistorialEquino (
    IN p_idEquino INT
)
BEGIN
    SELECT H.idHistorialHerrero, H.fecha, H.trabajoRealizado, H.herramientasUsadas, E1.descripcionEstado AS estadoInicio,
           E2.descripcionEstado AS estadoFin, H.observaciones
    FROM HistorialHerrero H
    JOIN EstadoHerramienta E1 ON H.estadoInicio = E1.idEstado
    LEFT JOIN EstadoHerramienta E2 ON H.estadoFin = E2.idEstado
    WHERE H.idEquino = p_idEquino
    ORDER BY H.fecha DESC;
END //

DELIMITER ;


-- 4. Procedimiento para Actualizar el Estado Final de una Herramienta Usada -------------------------
DELIMITER //

CREATE PROCEDURE ActualizarEstadoFinalHerramientaUsada (
    IN p_idHerramientasUsadas INT,
    IN p_estadoFin INT
)
BEGIN
    UPDATE HerramientasUsadasHistorial
    SET estadoFin = p_estadoFin
    WHERE idHerramientasUsadas = p_idHerramientasUsadas;
END //

DELIMITER ;

-- -5. Procedimiento para Consultar el Estado Actual de Herramientas-----------------------------------
DELIMITER //

CREATE PROCEDURE ConsultarEstadoActualHerramientas ()
BEGIN
    SELECT H.idHerramienta, H.descripcionEstado AS estadoActual, HU.estadoInicio, HU.estadoFin
    FROM HerramientasUsadasHistorial HU
    JOIN EstadoHerramienta H ON HU.estadoFin = H.idEstado
    ORDER BY HU.idHistorialHerrero DESC;
END //

DELIMITER ;


-- -----------6. Procedimiento para Insertar un Nuevo Estado de Herramienta en EstadoHerramienta-------
DELIMITER //

CREATE PROCEDURE InsertarEstadoHerramienta (
    IN p_descripcionEstado VARCHAR(50)
)
BEGIN
    INSERT INTO EstadoHerramienta (descripcionEstado)
    VALUES (p_descripcionEstado);
END //

DELIMITER ;

