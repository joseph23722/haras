-- agregar historial
DROP PROCEDURE IF EXISTS `InsertarHistorialHerrero`;

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
END ;

-- listar historial herrero
DROP PROCEDURE IF EXISTS `ConsultarHistorialEquino`;

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
END ;

-- Procedimiento para listar tipos de trabajos
DROP PROCEDURE IF EXISTS `spu_listar_tipos_trabajos`;

CREATE PROCEDURE spu_listar_tipos_trabajos()
BEGIN
    SELECT idTipoTrabajo, nombreTrabajo
    FROM TiposTrabajos;
END ;


-- Procedimiento para listar herramientas
DROP PROCEDURE IF EXISTS `spu_listar_herramientas`;

CREATE PROCEDURE spu_listar_herramientas()
BEGIN
    SELECT idHerramienta, nombreHerramienta
    FROM Herramientas;
END ;

-- Procedimiento para agregar un nuevo tipo de trabajo
DROP PROCEDURE IF EXISTS `spu_agregar_tipo_trabajo`;

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
END ;

-- Procedimiento para agregar una nueva herramienta
DROP PROCEDURE IF EXISTS `spu_agregar_herramienta`;

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
END ;

-- sugerencias herrero
DROP PROCEDURE IF EXISTS `spu_ListarTiposYHerramientas`;

CREATE PROCEDURE spu_ListarTiposYHerramientas()
BEGIN
    -- Combinar TiposTrabajos y Herramientas en un solo resultado con IDs únicos
    SELECT 
        CONCAT('T-', idTipoTrabajo) AS id, -- Prefijo 'T-' para TiposTrabajos
        nombreTrabajo AS nombre,
        'Tipo de Trabajo' AS tipo
    FROM 
        TiposTrabajos
    UNION ALL
    SELECT 
        CONCAT('H-', idHerramienta) AS id, -- Prefijo 'H-' para Herramientas
        nombreHerramienta AS nombre,
        'Herramienta' AS tipo
    FROM 
        Herramientas
    ORDER BY 
        nombre ASC; -- Ordena por nombre de forma ascendente
END ;