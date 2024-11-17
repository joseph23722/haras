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
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error al insertar en HistorialHerrero';
    END;

    -- Validación de entrada
    IF p_idEquino IS NULL OR p_idUsuario IS NULL OR p_fecha IS NULL OR p_trabajoRealizado IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Campos obligatorios faltantes para la inserción.';
    END IF;

    -- Iniciar una transacción
    START TRANSACTION;

    -- Inserción en la tabla HistorialHerrero
    INSERT INTO HistorialHerrero (
        idEquino, idUsuario, fecha, trabajoRealizado, herramientasUsadas, observaciones
    ) VALUES (
        p_idEquino, p_idUsuario, p_fecha, p_trabajoRealizado, p_herramientasUsadas, p_observaciones
    );

    -- Confirmar la transacción
    COMMIT;
END //

DELIMITER ;




-- 2. Procedimiento para Consultar el Historial Completo de un Equino
-- Retorna el historial de trabajos de herrería para un equino específico, con detalles generales del trabajo
DELIMITER //
CREATE PROCEDURE ConsultarHistorialEquino (
    IN p_idEquino INT
)
BEGIN
    SELECT 
        H.idHistorialHerrero, 
        H.fecha, 
        H.trabajoRealizado, 
        H.herramientasUsadas, 
        H.observaciones,
        E.nombreEquino,              -- Agrega el nombre del equino
        T.tipoEquino                 -- Agrega el tipo de equino
    FROM 
        HistorialHerrero H
    INNER JOIN 
        Equinos E ON H.idEquino = E.idEquino
    INNER JOIN 
        TipoEquinos T ON E.idTipoEquino = T.idTipoEquino
    WHERE 
        H.idEquino = p_idEquino
    ORDER BY 
        H.fecha DESC;
END //
DELIMITER ;


CALL ConsultarHistorialEquino(1); -- Cambia "1" por un ID de prueba


-- 1. Procedimiento para listar tipos de trabajos (TiposTrabajos)
DELIMITER $$
CREATE PROCEDURE spu_listar_tipos_trabajos()
BEGIN
    SELECT idTipoTrabajo, nombreTrabajo, descripcion 
    FROM TiposTrabajos;
END $$
DELIMITER ;





