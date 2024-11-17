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

-- 2. Procedimiento para listar herramientas (Herramientas)
DELIMITER $$
CREATE PROCEDURE spu_listar_herramientas()
BEGIN
    SELECT idHerramienta, nombreHerramienta, descripcion 
    FROM Herramientas;
END $$
DELIMITER ;

-- 3.  Procedimiento para agregar un nuevo tipo de trabajo
DELIMITER $$
CREATE PROCEDURE spu_agregar_tipo_trabajo(
    IN _nombreTrabajo VARCHAR(100),
    IN _descripcion TEXT
)
BEGIN
    -- Verificar que no exista un tipo de trabajo con el mismo nombre
    IF EXISTS (SELECT 1 FROM TiposTrabajos WHERE nombreTrabajo = _nombreTrabajo) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El tipo de trabajo ya existe.';
    ELSE
        -- Insertar el nuevo tipo de trabajo
        INSERT INTO TiposTrabajos (nombreTrabajo, descripcion)
        VALUES (_nombreTrabajo, _descripcion);
    END IF;
END $$
DELIMITER ;

-- 4. Procedimiento para agregar una nueva herramienta
DELIMITER $$
CREATE PROCEDURE spu_agregar_herramienta(
    IN _nombreHerramienta VARCHAR(100),
    IN _descripcion TEXT
)
BEGIN
    -- Verificar que no exista una herramienta con el mismo nombre
    IF EXISTS (SELECT 1 FROM Herramientas WHERE nombreHerramienta = _nombreHerramienta) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La herramienta ya existe.';
    ELSE
        -- Insertar la nueva herramienta
        INSERT INTO Herramientas (nombreHerramienta, descripcion)
        VALUES (_nombreHerramienta, _descripcion);
    END IF;
END $$
DELIMITER ;


