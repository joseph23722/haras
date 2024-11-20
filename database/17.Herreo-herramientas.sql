-- agregar historial
DROP PROCEDURE IF EXISTS `spu_personal_registrar`;
DELIMITER $$
CREATE PROCEDURE InsertarHistorialHerrero (
    IN p_idEquino INT,
    IN p_idUsuario INT,
    IN p_fecha DATE,
    IN p_idTrabajo INT,      -- ID del trabajo realizado
    IN p_idHerramienta INT,  -- ID de la herramienta utilizada
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
    IF p_idEquino IS NULL OR p_idUsuario IS NULL OR p_fecha IS NULL OR p_idTrabajo IS NULL OR p_idHerramienta IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Campos obligatorios faltantes para la inserción.';
    END IF;

    -- Iniciar una transacción
    START TRANSACTION;

    -- Inserción en la tabla HistorialHerrero
    INSERT INTO HistorialHerrero (
        idEquino, idUsuario, fecha, idTrabajo, observaciones
    ) VALUES (
        p_idEquino, p_idUsuario, p_fecha, p_idTrabajo, p_observaciones
    );

    -- Obtener el ID generado para HistorialHerrero
    SET @idHistorialHerrero = LAST_INSERT_ID();

    -- Inserción en la tabla HerramientasUsadasHistorial
    INSERT INTO HerramientasUsadasHistorial (
        idHistorialHerrero, idHerramienta
    ) VALUES (
        @idHistorialHerrero, p_idHerramienta
    );

    -- Confirmar la transacción
    COMMIT;
END //
DELIMITER ;


-- listar historial herrero
DROP PROCEDURE IF EXISTS `ConsultarHistorialEquino`;
DELIMITER $$

CREATE PROCEDURE ConsultarHistorialEquino()
BEGIN
    SELECT 
        HH.idHistorialHerrero, 
        HH.fecha, 
        TT.nombreTrabajo AS TrabajoRealizado, 
        GROUP_CONCAT(H.nombreHerramienta SEPARATOR ', ') AS HerramientasUsadas, 
        HH.observaciones,
        E.nombreEquino,              
        TE.tipoEquino                 
    FROM 
        HistorialHerrero HH
    INNER JOIN 
        Equinos E ON HH.idEquino = E.idEquino
    INNER JOIN 
        TipoEquinos TE ON E.idTipoEquino = TE.idTipoEquino
    INNER JOIN 
        TiposTrabajos TT ON HH.idTrabajo = TT.idTipoTrabajo
    LEFT JOIN 
        HerramientasUsadasHistorial HUH ON HH.idHistorialHerrero = HUH.idHistorialHerrero
    LEFT JOIN 
        Herramientas H ON HUH.idHerramienta = H.idHerramienta
    GROUP BY 
        HH.idHistorialHerrero, 
        HH.fecha, 
        TT.nombreTrabajo, 
        HH.observaciones, 
        E.nombreEquino, 
        TE.tipoEquino
    ORDER BY 
        HH.fecha DESC;
END $$

DELIMITER ;



-- Procedimiento para listar tipos de trabajos
DROP PROCEDURE IF EXISTS `spu_listar_tipos_trabajos`;
DELIMITER $$
CREATE PROCEDURE spu_listar_tipos_trabajos()
BEGIN
    SELECT idTipoTrabajo, nombreTrabajo
    FROM TiposTrabajos;
END $$
DELIMITER ;



-- Procedimiento para listar herramientas
DROP PROCEDURE IF EXISTS `spu_listar_herramientas`;
DELIMITER $$
CREATE PROCEDURE spu_listar_herramientas()
BEGIN
    SELECT idHerramienta, nombreHerramienta
    FROM Herramientas;
END $$
DELIMITER ;



-- Procedimiento para agregar un nuevo tipo de trabajo
DROP PROCEDURE IF EXISTS `spu_agregar_tipo_trabajo`;
DELIMITER $$
CREATE PROCEDURE spu_agregar_tipo_trabajo(
    IN _nombreTrabajo VARCHAR(100)
)
BEGIN
    -- Verificar que no exista un tipo de trabajo con el mismo nombre
    IF EXISTS (SELECT 1 FROM TiposTrabajos WHERE nombreTrabajo = _nombreTrabajo) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El tipo de trabajo ya existe.';
    ELSE
        -- Insertar el nuevo tipo de trabajo
        INSERT INTO TiposTrabajos (nombreTrabajo)
        VALUES (_nombreTrabajo);
    END IF;
END $$
DELIMITER ;



-- Procedimiento para agregar una nueva herramienta
DROP PROCEDURE IF EXISTS `spu_agregar_herramienta`;
DELIMITER $$
CREATE PROCEDURE spu_agregar_herramienta(
    IN _nombreHerramienta VARCHAR(100)
)
BEGIN
    -- Verificar que no exista una herramienta con el mismo nombre
    IF EXISTS (SELECT 1 FROM Herramientas WHERE nombreHerramienta = _nombreHerramienta) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La herramienta ya existe.';
    ELSE
        -- Insertar la nueva herramienta
        INSERT INTO Herramientas (nombreHerramienta)
        VALUES (_nombreHerramienta);
    END IF;
END $$
DELIMITER ;


-- pruebas 
CALL spu_agregar_tipo_trabajo('Recorte de cascos');

CALL spu_agregar_herramienta('Lima para cascos');


-- sugerencias herrero
DELIMITER $$
DROP PROCEDURE IF EXISTS ListarTiposYHerramientas $$
CREATE PROCEDURE ListarTiposYHerramientas()
BEGIN
    -- Combina los resultados de TiposTrabajos y Herramientas en una sola tabla con una columna adicional para identificar el tipo de dato
    SELECT idTipoTrabajo AS id, nombreTrabajo AS nombre, 'Tipo de Trabajo' AS tipo
    FROM TiposTrabajos
    UNION ALL
    SELECT idHerramienta AS id, nombreHerramienta AS nombre, 'Herramienta' AS tipo
    FROM Herramientas;
END $$

DELIMITER ;


CALL ListarTiposYHerramientas();
